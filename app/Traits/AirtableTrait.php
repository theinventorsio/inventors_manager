<?php
namespace App\Traits;

use Tapp\Airtable\Airtable;
use Tapp\Airtable\Api\AirtableApiClient;

trait AirtableTrait
{
    /**
     * @param string $idRecord
     * @param string $linkForm
     */
    public function addFormLink(string $idRecord, string $linkForm)
    {
        $client = new AirtableApiClient(
            env("AIRTABLE_CLASSES_BASE"),
            env("AIRTABLE_CLASSES_TABLE"),
            env("AIRTABLE_KEY")
        );

        $airTable = new Airtable($client);

        $data = ["Reports link" => $linkForm];

        return $airTable->patch($idRecord, $data);
    }

    public function addReport(array $reports)
    {
        $client = new AirtableApiClient(
            env("AIRTABLE_REPORTS_BASE"),
            env("AIRTABLE_REPORTS_TABLE"),
            env("AIRTABLE_KEY"), null, true
        );

        return $client->massUpdate('post', $reports);
    }

    public function getAllClasses()
    {
        $client = new AirtableApiClient(
            env("AIRTABLE_CLASSES_BASE"),
            env("AIRTABLE_CLASSES_TABLE"),
            env("AIRTABLE_KEY")
        );

        $airTable = new Airtable($client);

        return $airTable->all();
    }
}
