<?php

namespace App\Action\Incident;

use App\Action\ActionInterface;
use App\Domain\Incident\Service\IncidentSettingsService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

final class UpdateIncidentSettingsAction extends IncidentAction implements ActionInterface
{
    #[Inject]
    private IncidentSettingsService $incidentSetting;

    public function action(): Response
    {
        if($setting = $this->getArg('setting')) {
            $this->incidentSetting->updateSetting(
                $setting,
                $this->request->getParsedBody(),
                $this->incident,
                $this->getUser()
            );
        }
        return $this->redirectFor('incident.settings', ['incident' => $this->incident->getId()]);
    }
}
