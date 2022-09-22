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
    public function addFormLinkAirTables(string $idRecord, string $linkForm)
    {
        $client = new AirtableApiClient(env("AIRTABLE_BASE"), env("AIRTABLE_TABLE"), env("AIRTABLE_KEY"));
        $airTable = new Airtable($client);

        $data = ["Reports link" => $linkForm];

        $airTable->patch($idRecord, $data);
    }
}
