<?php

namespace App\Action\Manage;

use App\Action\Action;
use App\Action\ActionInterface;
use Nyholm\Psr7\Response;

final class ManageHomeAction extends Action implements ActionInterface
{
    public function action(): Response
    {
        return $this->render('manage/manage.html.twig');
    }
}
