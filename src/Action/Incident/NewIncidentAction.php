<?php

namespace App\Action\Incident;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\Incident\Service\NewIncidentService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

final class NewIncidentAction extends Action implements ActionInterface
{
    #[Inject]
    private NewIncidentService $newIncidentService;

    public function action(): Response
    {
        $user = $this->getUser();
        $incident = $this->newIncidentService->createNewIncident($this->getRequest()->getParsedBody(), $user);
        return $this->render('home/home.html.twig');
    }
}
