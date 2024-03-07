<?php

namespace App\Action\Incident;

use App\Action\ActionInterface;
use App\Domain\Agency\Service\FetchAgencyService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

final class ViewIncidentSettingsAction extends IncidentAction implements ActionInterface
{
    #[Inject]
    private FetchAgencyService $agencyService;

    public function action(): Response
    {
        return $this->render('incident/settings.html.twig', [
            'incident' => $this->incident,
            'agencies' => $this->agencyService->getAgenciesWithRoles()
        ]);
    }
}
