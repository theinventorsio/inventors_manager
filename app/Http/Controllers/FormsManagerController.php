<?php

namespace App\Http\Controllers;

use App\Traits\AirtableTrait;
use App\Traits\FormsServiceTrait;
use App\Traits\TablesManagerTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;


class FormsManagerController extends Controller
{
    use FormsServiceTrait;
    use AirtableTrait;
    use TablesManagerTrait;

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createForm(Request $request): JsonResponse
    {
        $formId        = env('GOOGLE_DEFAULT_FORM');
        $title         = $request->input('title');
        $idRecord      = $request->input('idRecord');
        $students      = $request->input('students') ?? '';
        $fieldName     = 'Presenças de Inscritos';
        $studentsArray = explode(',', $students);

        if($this->formsExists('id_class', $idRecord)) {
            return response()->json(['success' => false, 'msg' => 'Class/Form já existem']);
        }

        try {
            $service = $this->driveConn();

            $newFile = new \Google_Service_Drive_DriveFile();
            $newFile->setName($title);

            $copyFile = $service->files->copy($formId, $newFile);
            $fileId   = $copyFile->getId();

            if(empty($fileId)) {
                return response()->json(['success' => false, 'msg' => 'Error copying file']);
            }

            $body = empty($students) ? [] : ['values' => $studentsArray, 'fieldName' => $fieldName];

            $responseForm = $this->updateForm($fileId, $body, $title);

            if(!$responseForm['success']) {
                $service->files->delete($fileId);
                return response()->json(['success' => false, 'msg' => 'Error updating form data']);
            }

            $link = str_replace('{id}', $fileId, env('GOOGLE_DEFAULT_LINK_FORM'));

            $responseAir = $this->addFormLink($idRecord, $link);

            if(!empty($responseAir->get('error'))) {
                $service->files->delete($fileId);
                $errorMsg = 'Airtable ->' . $responseAir->get('error')['message'];
                $this->createFormDB($idRecord, $title, $students, 'FALSE', $errorMsg);

                return response()->json(['status' => false, 'message' => $errorMsg], 500);
            }

            $this->formCreateDB($idRecord, $fileId);
            $this->createFormDB($idRecord, $title, $students);
        } catch(\Exception $err) {
            $this->createFormDB($idRecord, $title, $students, 'ERROR', $err);

            return response()->json(['status' => false, 'message' => $err], 500);
        }

        return response()->json(["success" => true, "msg" => "Success creating form and updating airtable"]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function updateStudents(Request $request): array
    {
        $formId = $request->input('formId');
        $students = $request->input('students');

        if(empty($students) || empty($formId)) {
            return ['success' => false, 'msg' => 'missing value in students or formId'];
        }

        try {
            $body   = [
                "values"    => explode(',', $students),
                "fieldName" => 'Presenças de Inscritos'
            ];

            $update = $this->updateForm($formId, $body);

            $this->updateFormCreateDB($formId, $students);

            return $update;
        } catch(\Google\Exception | \Exception $e) {
            $this->updateFormCreateDB($formId, $students, 'ERROR', $e);

            return ['success' => false, 'error' => 'error updating'];
        }
    }
}
