<?php

namespace App\Action\Agency;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\Agency\Service\AgencyLogoUploadService;
use App\Domain\Agency\Service\AgencyUpdaterService;
use App\Domain\Agency\Service\FetchAgencyService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class EditAgencyAction extends Action implements ActionInterface
{
    #[Inject()]
    private FetchAgencyService $agencyService;

    #[Inject()]
    private AgencyUpdaterService $updaterService;

    public function action(): Response
    {
        $agency = $this->agencyService->getAgency($this->getArg('agency'));

        if('POST' === $this->request->getMethod()) {
            $this->updaterService->updateAgency(
                $agency,
                $this->request->getParsedBody(),
                $this->request->getUploadedFiles()
            );
            $this->addSuccessMessage("Agency successfully updated");
            return $this->redirectFor('agency.view', ['agency' => $agency->getId()]);
        }
        return $this->render('manage/agency/edit.html.twig', [
            'agency' => $agency,
            'activetab' => 'agency'
        ]);
    }
}
