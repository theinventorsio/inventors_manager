<?php

namespace App\Traits;

use Google\Exception;
use Google\Service\Drive;
use Google\Service\Forms;
use Google\Service\Forms\BatchUpdateFormRequest;
use Google\Service\Forms\Option;
use GuzzleHttp\Client;

trait FormsServiceTrait
{
    public function __construct()
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . env('GOOGLE_APPLICATION_CREDENTIALS'));
    }

    /**
     * @return \Google_Service_Forms
     */
    private function formConn(): \Google_Service_Forms
    {
        $scopes = [
            Forms::FORMS_BODY,
            Forms::FORMS_BODY_READONLY,
            Forms::FORMS_RESPONSES_READONLY
        ];

        $client = $this->conn($scopes);

        return new \Google_Service_Forms($client);
    }

    /**
     * @return \Google_Service_Drive
     */
    private function driveConn(): \Google_Service_Drive
    {
        $scopes = [
            Drive::DRIVE,
            Drive::DRIVE_FILE,
        ];

        $client = $this->conn($scopes);

        return new \Google_Service_Drive($client);
    }

    /**
     * @param array $scopes
     * @return \Google_Client
     */
    public function conn(array $scopes): \Google_Client
    {
        $client = new \Google_Client();
        $client->setHttpClient(new Client(array(
            'verify' => false
        )));

        $client->useApplicationDefaultCredentials();
        $client->setIncludeGrantedScopes(true);
        $client->setAccessType('offline');

        $client->setScopes($scopes);

        return $client;
    }

    /**
     * @param string $formId
     * @param array $body
     * @param string $title
     */
    public function updateForm(string $formId, array $body = [], string $title = '')
    {
        $values     = $body['values'];
        $fieldName  = $body['fieldName'];

        $service = $this->formConn();

        $form       = $service->forms->get($formId);

        $requests = [];

        if(!empty($title)) {
            $info = $form->getInfo();
            $info->setTitle($title);
            $form->setInfo($info);

            $requests[] = [
                "updateFormInfo" => [
                    "info" => ["title" => $title],
                    "updateMask" => '*'
                ]
            ];
        }

        if(!empty($body)) {
            $formItems  = $form->getItems();

            foreach($formItems as $key=>$item){
                if($item['title'] == $fieldName) {
                    $options = [];

                    foreach($values as $value) {
                        $option = new Option();
                        $option->setValue($value);
                        $options[] = $option;
                    }

                    $item->getQuestionItem()->getQuestion()->getChoiceQuestion()->setOptions($options);

                    $requests[] = [
                        "updateItem" => [
                            "location" => ["index"=>$key],
                            "item" => $item->toSimpleObject(),
                            "updateMask" => '*'
                        ]
                    ];
                }
            }

            $form->setItems($formItems);

            $batch = new BatchUpdateFormRequest();
            $batch->setRequests($requests);

            $service->forms->batchUpdate($formId, $batch);
        }
    }
}
