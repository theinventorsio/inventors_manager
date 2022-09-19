<?php

namespace App\Http\Controllers;

use App\Traits\FormsService;
use Google\Service\Drive\DriveFile;
use Google\Service\Forms\BatchUpdateFormRequest;
use Google\Service\Forms\Option;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FormsManagerController extends Controller
{
    use FormsService;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return [1, 2];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return array
     */
    public function createForm(Request $request): array
    {
        $title  = $request->input('title');
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

        return ["formLink" => str_replace('{id}', $fileId, env('GOOGLE_DEFAULT_LINK_FORM'))];
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
