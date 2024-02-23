<?php

namespace App\Action\Agency;

use App\Action\Action;
use App\Domain\Agency\Service\AgencyLogoUploadService;
use App\Domain\Agency\Service\FetchAgencyService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class ViewAgencyAction extends Action
{
    #[Inject()]
    private FetchAgencyService $agencyService;

    public function action(): Response
    {
        return $this->render('manage/agency/agency.html.twig', [
            'agency' => $this->agencyService->getAgency($this->getArg('agency'))
        ]);
    }
}
