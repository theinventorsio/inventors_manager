<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncronizeMigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncronize:migrate';

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
     * @return int
     */
    public function handle()
    {
        $classes = $this->getAllClasses();

        $addClassesToCreateForm = [];
        $addClassesToForms = [];

        return json_encode($classes);

        foreach($classes as $value) {
            $reportLink = $value['fields']['Reports link'];

            if(!empty($reportLink)) {
                $students = implode(', ', $value['fields']['Students Array']);

                $reportLinkSplit = explode('/', $reportLink);
                $reportLink = $reportLinkSplit[sizeof($reportLinkSplit)-2];

                $addClassesToCreateForm[] = [
                    'id_class' => $value['id'],
                    'title' => $value['fields']['Name'],
                    'students' => $students
                ];

                $addClassesToForms[] = [
                    'id_class' => $value['id'],
                    'id_form' => $reportLink,
                    'active' => true
                ];
            }
        }

        $this->insertCreateForm($addClassesToCreateForm);
        $this->insertForms($addClassesToForms);

        return [];
    }
}
