<?php

namespace App\Action\Manage;

use App\Action\Action;
use Nyholm\Psr7\Response;

final class ManageHomeAction extends Action
{
    public function action(): Response
    {
        return $this->render('manage/manage.html.twig');
    }
}
