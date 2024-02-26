<?php

namespace App\Domain\Agency\Service;

use App\Domain\Agency\Repository\AgencyMembershipRepository;
use App\Domain\Agency\Repository\AgencyRepository;
use App\Domain\User\Data\User;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\Service\FetchUserService;
use DI\Attribute\Inject;

class AgencyMembershipService
{
    #[Inject()]
    private FetchUserService $userService;

    #[Inject()]
    private AgencyRepository $agencyRepository;

    #[Inject()]
    private AgencyMembershipRepository $membershipRepository;

    public function changeMembership(int $target, int $agency, User $creator)
    {
        $this->membershipRepository->insertOrUpdateMembership($target, $agency, $creator->getId());
    }

    // public function getAgencyMembershipChanges(
    //     int $target,
    //     array $data
    // ): array {
    //     // $this->validateChanges();
    //     $target = $this->userService->getUser($target);
    //     if(!empty($data['agency'])) {
    //         $membershipAdjustment = [];
    //         foreach($data['agency'] as $a) {
    //             $membershipAdjustment[] = [
    //                 'agency' => $this->agencyRepository->findOneBy([$a], 'a.id = ?'),
    //                 'target' => $target,
    //                 'title' => $data[$a.'-title'],
    //                 'action' => 'add'
    //             ];
    //         }
    //         return $membershipAdjustment;
    //     }
    // }

    // public function confirmAgencyChanges(array $changes, User $creator)
    // {
    //     foreach($changes as $c) {
    //         $this->membershipRepository->insertNewAgencyMembership(
    //             $c['target'],
    //             $c['agency'],
    //             $c['title'],
    //             $creator->getId()
    //         );
    //     }
    //     var_dump($changes);
    // }

}
