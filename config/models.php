<?php

use App\Models\CreateForm;
use App\Models\FormResponses;
use App\Models\Forms;

return [
    'create_form_request' => CreateForm::class,
    'form_airtable' => Forms::class,
    'form_response' => FormResponses::class,
    'update_form_request' => [
        "query" => "SELECT 
                    ufr.id, fa.id_form, fa.id_class, ufr.students, ufr.status, ufr.created_at 
                    FROM update_form_request ufr
                    LEFT JOIN form_airtable fa ON ufr.id_form_airtable = fa.id ",
        "columns" => [
            "id", "id_form", "id_class", "students", "status", "created_at"
        ]
    ]
];