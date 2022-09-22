<?php

namespace App\Http\Controllers;

use App\Traits\AirtableTrait;
use App\Traits\FormsServiceTrait;
use Illuminate\Http\Request;
use Tapp\Airtable\Airtable;
use Tapp\Airtable\Api\AirtableApiClient;

class FormsManagerController extends Controller
{
    use FormsServiceTrait;
    use AirtableTrait;

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createForm(Request $request): \Illuminate\Http\JsonResponse
    {

        try {
            $title  = $request->input('title');
            $idRecord = $request->input('idRecord',);
            $formId = env('GOOGLE_DEFAULT_FORM');

            $service = $this->driveConn();

            $newFile = new \Google_Service_Drive_DriveFile();
            $newFile->setName($title);

            $copyFile = $service->files->copy($formId, $newFile);
            $fileId   = $copyFile->getId();

            $values    = $request->input('students') ?? [];
            $fieldName = $request->input('fieldName') ?? '';

            $body = empty($values) || empty($fieldName) ? [] : ['values' => $values, 'fieldName' => $fieldName];

            $this->updateForm(
                $fileId,
                $body,
                $title
            );

            $link = str_replace('{id}', $fileId, env('GOOGLE_DEFAULT_LINK_FORM'));

            $this->addFormLinkAirTables($idRecord, $link);
        } catch(\Exception $err) {
            return response()->json([
                'status' => false,
                'message' => $err
            ], 500);
        }

        return response()->json(["success" => true]);
    }

    /**
     * @param Request $request
     * @param array|null $body
     * @return bool[]
     */
    public function updateStudents(Request $request): array
    {
        $formId = $request->input('formId');

        $body   = [
            "values"    => $request->input('values'),
            "fieldName" => $request->input('fieldName')
        ];

        $this->updateForm($formId, $body);

        return ['success' => true];
    }
}
