<?php

namespace App\Domain\Incident\Repository;

use App\Repository\Repository;

class IncidentAgencyRepository extends Repository
{
    public function updateIncidentAgencies(int $incident, int $agency, bool $status): void
    {
        $result = $this->db->insertOnDuplicateKeyUpdate('incident_agency', [
            'incident' => $incident,
            'agency' => $agency,
            'status' => $status,
        ], [
            'status' => !$status
        ]);

        var_dump($result);

    }

}
