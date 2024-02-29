<?php

namespace App\Domain\Agency\Service;

use App\Domain\Agency\Data\Agency;
use App\Domain\Agency\Exception\AgencyNotFoundException;
use App\Domain\Agency\Repository\AgencyRepository;
use DI\Attribute\Inject;

class FetchAgencyService
{
    #[Inject()]
    private AgencyRepository $agencyRepository;

    public function getAgency(int $id): Agency
    {
        $agency = $this->agencyRepository->getAgency($id);
        if(!$agency) {
            throw new AgencyNotFoundException();
        }
        return $agency;
    }

    public function getUsersForAgency(int $id): array
    {
        return $this->agencyRepository->getUsersForAgency($id);
    }

}
