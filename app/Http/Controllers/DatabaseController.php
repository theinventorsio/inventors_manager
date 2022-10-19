<?php

namespace App\Http\Controllers;

use App\Models\CreateForm;
use App\Models\FormResponses;
use App\Models\Forms;
use App\Models\UpdateForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{

    /**
     * @param Request $request
     * @return void
     */
    public function getDBtoCSV(Request $request)
    {
        $table = $request->input('table') ?? 'create_form_request';
        $startDate = $request->input('start_date') ?? null;
        $endDate = $request->input('end_date') ?? null;

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=product-' . date("Y-m-d-h-i-s") . '.csv');
        $output = fopen('php://output', 'w');

        $columns = DB::getSchemaBuilder()->getColumnListing($table);
        fputcsv($output, $columns);

        $tables = [
            'create_form_request' => CreateForm::class,
            'form_airtable' => Forms::class,
            'form_response' => FormResponses::class,
            'update_form_request' => UpdateForm::class
        ];

        $rows = [];

        if(!empty($startDate)) {
            $rows = $tables[$table]::where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate ?? now())
                ->get();
        } else {
            $rows = $tables[$table]::get();
        }


        if (count($rows) > 0) {

            foreach ($rows as $row) {

                $product_row = [];

                foreach($columns as $column) {
                    $product_row[] = $row[$column];
                }

                fputcsv($output, $product_row);
            }
        }
    }
}
