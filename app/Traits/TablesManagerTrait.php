<?php
namespace App\Traits;

use App\Models\CreateForm;
use App\Models\UpdateForm;
use App\Models\Forms;
use App\Models\FormResponses;
use Illuminate\Support\Facades\DB;

trait TablesManagerTrait
{
    /**
     * Create record in CreateForm
     *
     * @param string $idRecord
     * @param string $title
     * @param string $students
     * @param string $status
     * @param string|null $observations
     * @return void
     */
    public function createFormDB(string $idRecord, string $title, string $students, string $status = 'TRUE', string $observations = null)
    {
        CreateForm::create([
            'id_class'           => $idRecord,
            'title'              => $title,
            'students'           => $students,
            'status'             => $status,
            'status_observation' => $observations
        ]);
    }

    /**
     * Create record in Forms
     *
     * @param string $idClass
     * @param string $idForm
     * @param bool $active
     * @return void
     */
    public function formCreateDB(string $idClass, string $idForm, bool $active = true)
    {
        Forms::create([
            'id_class' => $idClass,
            'id_form'  => $idForm,
            'active'   => $active
        ]);
    }

    /**
     * Check if exists record in Forms
     *
     * @param string $key
     * @param string|int|null $value
     * @return mixed
     */
    public function formsExists(string $key, ?string $value): bool
    {
        return Forms::where($key, $value)->exists();
    }

    /**
     * Creata Entry in UpdateForm
     *
     * @param string $idClass
     * @param string $idForm
     * @param bool $active
     * @return void
     */
    public function updateFormCreateDB(string $formId, string $studentsArray, string $status = 'TRUE')
    {
        UpdateForm::create([
            'id_form'  => $formId,
            'students' => $studentsArray,
            'status'   => $status
        ]);
    }

    /**
     * @return array
     */
    public function getFormsToGetResponses(): array
    {
        $forms = DB::select(
        "SELECT fa.id, fa.id_form, fa.id_class, fr2.id_response, fr2.create_date_response 
                FROM form_airtable fa
                LEFT JOIN (SELECT id, id_form_airtable, MAX(create_date_response) FROM form_response GROUP BY id_form_airtable DESC) fr ON fr.id_form_airtable = fa.id
                LEFT JOIN form_response fr2 ON fr.id = fr2.id;"
        );

        return $forms;
    }

    public function updateFormsLastResponse($formId, $idLastResponse)
    {
        Forms::where('id', $formId)->update(['id_last_response' => $idLastResponse]);
    }

    /**
     * @param $data
     * @return void
     */
    public function insertFormResponses(array $data)
    {
        FormResponses::insert($data);
    }

    /**
     * @param $data
     * @return void
     */
    public function insertForms(array $data)
    {
        Forms::insert($data);
    }

    /**
     * @param $data
     * @return void
     */
    public function insertCreateForm(array $data)
    {
        CreateForm::insert($data);
    }
}
