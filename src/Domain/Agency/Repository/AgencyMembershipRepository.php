<?php

namespace App\Domain\Agency\Repository;

use App\Domain\Agency\Data\Agency;
use App\Domain\User\Data\User;
use App\Repository\QueryBuilder;
use App\Repository\Repository;

class AgencyMembershipRepository extends Repository
{
    public string $table = 'user_agency';

    public function insertNewAgencyMembership(int $target, int $agency, string $title, int $creator): void
    {
        $this->db->insert($this->table, [
            'target' => $target,
            'agency' => $agency,
            'title' => $title,
            'creator' => $creator
        ]);
    }

    public function getAgenciesForUser(int $user): array
    {
        $this->setEntity(Agency::class);
        $sql = QueryBuilder::select('user_agency ua', [
            'a.id',
            'a.name',
            'a.logo',
            'a.created',
            'a.fullname',
            'a.location',
            'ua.title'
        ], [
            'ua.target = ?'
        ], [
            'agency a ON ua.agency = a.id'
        ], [
            'ua.id'
        ]);
        return $this->run($sql, [$user])->getResults();
    }

}
