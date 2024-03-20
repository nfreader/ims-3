<?php

namespace App\Action\Log;

use App\Action\Action;
use App\Action\ActionInterface;
use Doctrine\DBAL\ParameterType;
use Nyholm\Psr7\Response;
use Symfony\Component\Filesystem\Path;

final class ViewDoctrineLog extends Action implements ActionInterface
{
    public function action(): Response
    {
        if('POST' === $this->request->getMethod()) {
            unlink(Path::normalize("../logs/doctrine_db.log"));
            $this->addSuccessMessage("Log file has been cleared");
            return $this->redirectFor('log.doctrine');
        }
        $search = $_GET['search'] ?? false;
        $lines = [];
        foreach(file(Path::normalize("../logs/doctrine_db.log")) as $line) {
            $l = null;
            if($search) {
                if(str_contains($line, $search)) {
                    $l = json_decode($line);
                }
            } else {
                $l = json_decode($line);
            }
            if($l) {
                foreach($l->context->trace as &$t) {
                    if(str_starts_with($t->class, "App\\")) {
                        $t->ignore = true;
                    } else {
                        $t->ignore = false;
                    }
                }
                foreach($l->context->types as &$t) {
                    $t = ParameterType::cases()[$t]->name;
                }
                $l->context->types = (array) $l->context->types;
                $l->context->params = (array) $l->context->params;
                $lines[] = $l;
            }
        }

        return $this->render('log/viewlog.html.twig', [
            'lines' => $lines,
            'url' => 'log.doctrine'
        ]);
    }
}
