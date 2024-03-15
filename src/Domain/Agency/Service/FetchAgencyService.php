<?php

namespace App\Domain\Agency\Service;

use App\Domain\Agency\Data\Agency;
use App\Domain\Agency\Exception\AgencyNotFoundException;
use App\Domain\Agency\Repository\AgencyRepository;
use App\Domain\Role\Repository\RoleRepository;
use DI\Attribute\Inject;

class FetchAgencyService
{
    #[Inject()]
    private AgencyRepository $agencyRepository;

    #[Inject()]
    private RoleRepository $roleRepository;

    public function getAgency(int $id): Agency
    {
        $agency = $this->agencyRepository->getAgency($id);
        if(!$agency) {
            throw new AgencyNotFoundException();
        }
        return $agency;
    }

    public function getAgenciesWithRoles(): array
    {
        $agencies = $this->agencyRepository->getAgencies();
        $roles = $this->roleRepository->getAllRoles();
        foreach($roles as $k => $r) {
            foreach($agencies as $a) {
                if($r->getAgency() === $a->getId()) {
                    $a->addRole($r);
                    unset($roles[$k]); //Micro-Optimization!!
                }
            }
        }
        return $agencies;
    }

}
