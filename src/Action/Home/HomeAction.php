<?php

namespace App\Action\Home;

use App\Action\Action;
use App\Domain\Incident\Repository\IncidentRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

final class HomeAction extends Action
{
    #[Inject]
    private IncidentRepository $incidentRepository;

    public function action(): Response
    {
        $incidents = null;
        if($this->getUser()) {
            $incidents = $this->incidentRepository->listIncidents();
        }
        return $this->render('home/home.html.twig', [
            'incidents' => $incidents
        ]);
    }
}
