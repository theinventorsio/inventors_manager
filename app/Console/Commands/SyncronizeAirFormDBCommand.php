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
     * @return int
     */
    public function handle()
    {
        $classes = $this->getAllClasses();

        $addClassesToCreateForm = [];
        $addClassesToForms = [];

        foreach($classes as $value) {
            $reportLink = $value['fields']['Reports link'];

            if(!empty($reportLink)) {
                $students = implode(', ', $value['fields']['Students Array']);

                $reportLink = Arr::last(explode('/', rtrim($reportLink, '/viewform')));

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
