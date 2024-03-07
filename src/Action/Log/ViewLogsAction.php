<?php

namespace App\Action\Log;

use App\Action\Action;
use App\Action\ActionInterface;
use Nyholm\Psr7\Response;

final class ViewLogsAction extends Action implements ActionInterface
{
    public function action(): Response
    {
        return $this->render('log/listing.html.twig', [
            'files' => [
                [
                    'route' => 'log.db',
                    'name' => 'Database',
                    'icon' => 'fas fa-database'
                ]
            ]
        ]);
    }
}
