<?php

namespace App\Http\Controllers;

use App\Models\CreateForm;
use App\Models\FormResponses;
use App\Models\Forms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    /**
     * @param Request $request
     * @return void|array
     */
    public function getDBtoCSV(Request $request)
    {
        $table = $request->input('table') ?? 'create_form_request';
        $startDate = $request->input('start_date') ?? null;
        $endDate = $request->input('end_date') ?? null;
        $toCsv = $request->input('to_csv') ?? true;

        $tables = [
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

        $tableValue = $tables[$table] ?? null;

        if(empty($tableValue)) {
            return ["success" => false, "error" => "table doesn't exists"];
        }

        $rows = $this->getData($tableValue, $startDate, $endDate);

        if(!$toCsv) {
            return $rows;
        }

        $this->toCsv($table, $rows, $tableValue['columns'] ?? null);
    }

    /**
     * @param $tableValue
     * @param string $startDate
     * @param string|null $endDate
     * @return array|mixed
     */
    private function getData($tableValue, string $startDate, ?string $endDate)
    {
        $rows = [];

        if(!empty($startDate)) {
            if(!is_array($tableValue)) {
                $rows = $tableValue::where('created_at', '>=', $startDate)
                    ->where('created_at', '<=', $endDate ?? now())
                    ->get();
            } else {
                $where = " WHERE ufr.created_at BETWEEN '$startDate' AND '" . ($endDate ?? now()) . "'";
                $rows  = DB::select($tableValue['query'] . $where);
            }

        } else {
            $rows = !is_array($tableValue) ? $tableValue::get() : DB::select($tableValue['query']);
        }

        if(is_array($tableValue)) {
            $rows = json_decode(json_encode($rows), true);
        }

        return $rows;
    }

    /**
     * @param string $table
     * @param array $rows
     * @param array|null $columns
     * @return void
     */
    private function toCSV(string $table, array $rows, ?array $columns): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename={$table}-" . date("Y-m-d-h-i-s") . '.csv');

        $output = fopen('php://output', 'w');
        $columns = !empty($columns) ? $columns : DB::getSchemaBuilder()->getColumnListing($table);
        fputcsv($output, $columns);

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
