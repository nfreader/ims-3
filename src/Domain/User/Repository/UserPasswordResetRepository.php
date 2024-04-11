<?php

namespace App\Domain\User\Repository;

use App\Repository\Repository;
use Doctrine\DBAL\ParameterType;

class UserPasswordResetRepository extends Repository
{
    public ?string $table = 'user_password_reset';

    public function insertNewPasswordReset(int $user, string $selector, string $validator)
    {
        $this->purgeExpiredCodes();
        $queryBuilder = $this->qb();
        $queryBuilder->insert($this->table);
        $queryBuilder->values([
            'user' => $queryBuilder->createNamedParameter($user),
            'selector' => $queryBuilder->createNamedParameter($selector),
            'validator' => $queryBuilder->createNamedParameter($validator)
        ]);
        $queryBuilder->executeStatement();
    }

    public function getResetCode(string $code)
    {
        $this->purgeExpiredCodes();
        $queryBuilder = $this->qb();
        $queryBuilder->select(...['user', 'code,', 'created'])
        ->from($this->table)
        ->where('code = '. $queryBuilder->createNamedParameter($code, ParameterType::STRING))
        ->andWhere('DATE_SUB(CURDATE(),INTERVAL 10 MINUTE) <= created');
        $result = $queryBuilder->executeQuery();
        return $result->fetchAssociative();
    }

    public function getTokenBySelector(string $selector)
    {
        $this->purgeExpiredCodes();
        $queryBuilder = $this->qb();
        $queryBuilder->select(...[
            'user',
            'validator',
        ])->from($this->table)
        ->where('selector = '. $queryBuilder->createNamedParameter($selector))
        ->andWhere('DATE_SUB(CURDATE(),INTERVAL 10 MINUTE) <= created');
        $result = $queryBuilder->executeQuery();
        return $result->fetchAssociative();
    }

    public function deleteResetToken(string $selector)
    {
        $queryBuilder = $this->qb();
        $queryBuilder->delete($this->table)
        ->where('selector = '.$queryBuilder->createNamedParameter($selector));
        $queryBuilder->executeStatement();
    }

    private function purgeExpiredCodes(): void
    {
        $queryBuilder = $this->qb();
        $queryBuilder->delete($this->table);
        $queryBuilder->where('DATE_SUB(CURDATE(), INTERVAL 10 MINUTE) <= created');
        $queryBuilder->executeStatement();
    }

}
