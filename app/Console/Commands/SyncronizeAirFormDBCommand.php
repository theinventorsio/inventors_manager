<?php

namespace App\Console\Commands;

use App\Traits\AirtableTrait;
use App\Traits\FormsServiceTrait;
use App\Traits\TablesManagerTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class SyncronizeAirFormDBCommand extends Command
{
    use FormsServiceTrait;
    use AirtableTrait;
    use TablesManagerTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncronize:total';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $conn = $this->formConn();

        $forms = $this->getFormsToGetResponses();

        $responsesToAdd = [];
        $formConfig = app('config')->get('forms');
        $formAnswers = $formConfig['dev']['form']['answers'];

        foreach($forms as $form) {
            $formId = $form->id_form;

            $parameters = [$formId];

            if(!empty($form->create_date_response)) {
                $parameters[] = [
                    'filter' => ['timestamp > '.$form->create_date_response]
                ];
            }

            try {
                $res = $conn->forms_responses->listFormsResponses(...$parameters);
                $responses = $res->getResponses();
            } catch (\Exception $e) {
                return ['success' => false, 'msg' => $e, 'body' => $parameters];
            }

            if(!empty($responses)) {
                $dbToAdd = [];

                $responses = array_reverse($responses);

                foreach($responses as $response) {
                    if($this->formResponseExists($response->responseId)) {
                        echo $response->responseId;
                        continue;
                    }

                    $response = $response->toSimpleObject();
                    $answers = [];
                    $insert = [
                        'id_form_airtable' => $form->id,
                        'id_response' => $response->responseId,
                        'create_date_response' => $response->lastSubmittedTime
                    ];

                    foreach($formAnswers as $key=>$value){
                        $data = data_get($response, 'answers.'.$key.'.textAnswers.answers.*.value', ['']);
                        $resp_value = sizeof($data) > 1 ? implode(', ', $data) : $data[0];

                        $answers[$value['airtable']] = $resp_value;
                        $insert[$value['db']] = $resp_value;
                    }

                    $dbToAdd[] = $insert;
                    $responsesToAdd[] = ['fields' => $answers];
                }

                if(!empty($dbToAdd)) {
                    $this->insertFormResponses($dbToAdd);
                }
            }
        }

        return $this->addReport($responsesToAdd);
    }
}
