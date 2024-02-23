<?php

namespace App\Action\Agency;

use App\Action\Action;
use App\Domain\Agency\Repository\AgencyRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class ListAgenciesAction extends Action
{
    #[Inject()]
    private AgencyRepository $agencyRepository;

    public function action(): Response
    {
        $agencies = $this->agencyRepository->getAgencies();
        return $this->render('manage/agency/listing.html.twig', [
            'agencies' => $agencies
        ]);
    }
}
