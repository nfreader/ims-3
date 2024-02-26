<?php

namespace App\Domain\Agency\Repository;

use App\Domain\Agency\Data\Agency;
use App\Domain\User\Data\User;
use App\Repository\DoctrineRepository;
use App\Repository\QueryBuilder;
use App\Repository\Repository;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

class AgencyMembershipRepository extends DoctrineRepository
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
        $queryBuilder = $this->qb();
        $queryBuilder->select(...AgencyRepository::COLUMNS);
        $queryBuilder->addSelect('ua.title');
        $queryBuilder->from('user_agency', 'ua');
        $queryBuilder->join('ua', 'agency', 'a', 'a.id = ua.agency');
        $queryBuilder->where('ua.target = '.$queryBuilder->createPositionalParameter($user));
        $result = $queryBuilder->executeQuery($queryBuilder->getSQL(), [$user]);
        $this->overrideMetadata(Agency::class);
        $return = [];
        foreach($result->fetchAllAssociative() as $r) {
            $return[] = new Agency(...$this->mapRow($r));
        }
        return $return;
    }
    // public function getAgenciesForUser(int $user): array
    // {
    //     $this->setEntity(Agency::class);
    //     $sql = QueryBuilder::select('user_agency ua', [
    //         'a.id',
    //         'a.name',
    //         'a.logo',
    //         'a.created',
    //         'a.fullname',
    //         'a.location',
    //         'a.active',
    //         'ua.title'
    //     ], [
    //         'ua.target = ?'
    //     ], [
    //         'agency a ON ua.agency = a.id'
    //     ], [
    //         'ua.id'
    //     ]);
    //     return $this->run($sql, [$user])->getResults();
    // }

    public function insertOrUpdateMembership(int $target, int $agency, int $creator)
    {
        try {
            $this->insertMembership($target, $agency, $creator);
        } catch (Exception $e) {
            $this->updateMembership($target, $agency);
        }
    }

    public function insertMembership(int $target, int $agency, int $creator)
    {
        $queryBuilder = $this->qb();
        $queryBuilder->insert($this->table);
        $queryBuilder->values([
            'target' => $queryBuilder->createPositionalParameter($target),
            'agency' => $queryBuilder->createPositionalParameter($agency),
            'creator' => $queryBuilder->createPositionalParameter($creator)
        ]);
        $queryBuilder->executeQuery($queryBuilder->getSQL(), [$target, $agency, $creator]);
    }

    public function updateMembership(int $target, int $agency)
    {
        $queryBuilder = $this->qb();
        $queryBuilder->update($this->table);
        $queryBuilder->set('status', '1 - status');
        $queryBuilder->where('target = '.$queryBuilder->createPositionalParameter($target));
        $queryBuilder->andWhere('agency = '. $queryBuilder->createPositionalParameter($agency));
        $queryBuilder->executeQuery($queryBuilder->getSQL(), [$target, $agency]);
    }

    // public function updateMembership(int $target, int $agency)
    // {
    //     $queryBuilder = $this->qb();
    //     $queryBuilder->in
    // }

}
