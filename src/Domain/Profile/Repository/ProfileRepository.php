<?php

namespace App\Domain\Profile\Repository;

use App\Domain\Profile\Data\ProfileSetting;
use App\Repository\Repository;

class ProfileRepository extends Repository
{
    public ?string $entityClass = ProfileSetting::class;

    public string $table = 'user_settings';

    public string $alias = 's';

    public function getProfileForUser(int $user): array
    {
        $queryBuilder = $this->qb();
        $queryBuilder
        ->select(...[
            's.name',
            's.value',
            's.autoload'
        ])
        ->from($this->table, $this->alias)
        ->where('s.user = '.$queryBuilder->createNamedParameter($user));
        $result = $queryBuilder->executeQuery();
        return $this->getResults($result);
    }

    public function setUserSettings(int $user, array $data)
    {
        //We clear all the existing settings for a user before we insert them again
        $queryBuilder = $this->qb();
        $queryBuilder->delete($this->table);
        $queryBuilder->where('user = '.$queryBuilder->createNamedParameter($user));
        $queryBuilder->executeQuery();

        $queryBuilder = $this->qb();
        foreach($data as $key => $value) {
            $queryBuilder->insert($this->table);
            // $queryBuilder->set($key, $queryBuilder->createNamedParameter($value));
            // $queryBuilder->set('user', $queryBuilder->createNamedParameter($user));
            $queryBuilder->values([
                'name' => $queryBuilder->createNamedParameter($key),
                'value' => $queryBuilder->createNamedParameter($value),
                'user' => $queryBuilder->createNamedParameter($user)
            ]);
            $queryBuilder->executeStatement();
        }
    }

}
