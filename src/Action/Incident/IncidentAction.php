<?php

namespace App\Action\Incident;

use App\Action\Action;
use App\Action\GetEntitiesInterface;
use App\Domain\Incident\Data\Incident;
use App\Domain\Incident\Service\FetchIncidentService;
use App\Domain\Permissions\Data\PermissionsEnum;
use JustSteveKing\StatusCode\Http;
use Psr\Container\ContainerInterface;
use Slim\Exception\HttpException;

class IncidentAction extends Action implements GetEntitiesInterface
{
    protected Incident $incident;

    public function __construct(
        protected ContainerInterface $container,
        private FetchIncidentService $incidentService
    ) {
        parent::__construct($container);
    }

    public function getEntities(): static
    {
        $this->incident = $this->incidentService->getIncident($this->getArg('incident'));
        if(!$this->getUser()->can(PermissionsEnum::VIEW_INCIDENT, $this->incident)) {
            throw new HttpException($this->getRequest(), "Your active role does not have permission to view this", Http::UNAUTHORIZED->value);
        }
        return $this;
    }

}
