<?php

namespace App\Domain\Agency\Service;

use App\Domain\Agency\Repository\AgencyRepository;
use DI\Attribute\Inject;

class AgencyCreationService
{
    #[Inject()]
    private AgencyRepository $agencyRepository;

    public function createNewAgency(string $name, ?string $logo, ?string $fullname, ?string $location): int
    {
        return $this->agencyRepository->insertNewAgency($name, $logo, $fullname, $location);
    }

}
