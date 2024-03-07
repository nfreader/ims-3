<?php

namespace App\Action\Home;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\Incident\Repository\IncidentRepository;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

final class HomeAction extends Action implements ActionInterface
{
    #[Inject]
    private IncidentRepository $incidentRepository;

    public function action(): Response
    {
        $incidents = null;
        if($this->getUser()) {
            $role = $this->getUser()->getActiveRole()?->getRoleId();
            $incidents = $this->incidentRepository->listIncidentsForActiveRole($role);
        }
        return $this->render('home/home.html.twig', [
            'incidents' => $incidents
        ]);
    }
}
