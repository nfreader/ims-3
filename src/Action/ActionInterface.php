<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;

interface ActionInterface
{
    public function action(): ResponseInterface;
}
