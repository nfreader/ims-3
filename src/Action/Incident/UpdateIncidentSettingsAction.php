<?php

namespace App\Action\Incident;

use App\Action\Action;
use App\Domain\Agency\Repository\AgencyRepository;
use App\Domain\Incident\Service\FetchIncidentService;
use App\Domain\Incident\Service\IncidentSettingsService;
use DI\Attribute\Inject;
use Exception;
use Nyholm\Psr7\Response;

final class UpdateIncidentSettingsAction extends Action
{
    #[Inject]
    private IncidentSettingsService $incidentSetting;

    #[Inject]
    private AgencyRepository $agencyRepository;

    public function action(): Response
    {
        if($setting = $this->getArg('setting')) {
            $this->incidentSetting->updateSetting(
                $setting,
                $this->request->getParsedBody(),
                $this->getArg('incident'),
                $this->getUser()
            );
        }
        return $this->json($this->getRequest()->getParsedBody());
    }
}
