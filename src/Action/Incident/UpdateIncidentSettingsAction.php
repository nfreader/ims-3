<?php

namespace App\Action\Incident;

use App\Action\ActionInterface;
use App\Domain\Agency\Service\FetchAgencyService;
use App\Domain\Incident\Service\IncidentSettingsService;
use App\Domain\Permissions\Data\PermissionsEnum;
use DI\Attribute\Inject;
use JustSteveKing\StatusCode\Http;
use Nyholm\Psr7\Response;
use Slim\Exception\HttpException;

final class UpdateIncidentSettingsAction extends IncidentAction implements ActionInterface
{
    #[Inject]
    private IncidentSettingsService $incidentSetting;

    #[Inject]
    private FetchAgencyService $agencyService;

    public function action(): Response
    {
        if(!$this->getUser()->can(PermissionsEnum::EDIT_INCIDENT, $this->incident)) {
            throw new HttpException($this->getRequest(), "Your active role does not have permission to perform these actions", Http::UNAUTHORIZED->value);
        }
        if('POST' === $this->request->getMethod()) {
            if($setting = $this->getArg('setting')) {
                $this->incidentSetting->updateSetting(
                    $setting,
                    $this->request->getParsedBody(),
                    $this->incident,
                    $this->getUser()
                );
            }
            return $this->redirectFor('incident.settings', ['incident' => $this->incident->getId()]);
        } else {
            $data = [];
            $data['incident'] = $this->incident;
            $data['activeTab'] = 'settings';

            switch ($this->getArg('setting')) {
                case 'settings':
                default:
                    $template = 'incident/settings/settings.html.twig';
                    break;
                case 'roles':
                    $template = 'incident/settings/roles.html.twig';
                    $data['agencies'] = $this->agencyService->getAgenciesWithRoles();
                    break;
            }
            return $this->render($template, $data);
        }
    }
}
