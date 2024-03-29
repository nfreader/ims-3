<?php

namespace App\Action\Agency;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\Agency\Service\AgencyLogoUploadService;
use App\Domain\Agency\Service\FetchAgencyService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class ViewAgencyAction extends Action implements ActionInterface
{
    #[Inject()]
    private FetchAgencyService $agencyService;

    public function action(): Response
    {
        $agency = $this->agencyService->getAgency($this->getArg('agency'));
        return $this->render('manage/agency/agency.html.twig', [
            'agency' => $agency,
            'activetab' => 'agency'
        ]);
    }
}
