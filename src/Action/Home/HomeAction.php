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
        if($this->getUser()) {
            if($this->getUser()?->isSudoMode()) {
                $this->addContext(
                    'incidents',
                    $this->incidentRepository->listIncidents()
                );
            } else {
                $this->addContext(
                    'incidents',
                    $this->incidentRepository->listIncidentsForActiveRole(
                        $this->getUser()->getActiveRole()?->getRoleId()
                    )
                );
            }
        }
        return $this->render('home/home.html.twig');
    }
}
