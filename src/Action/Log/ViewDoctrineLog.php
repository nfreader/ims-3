<?php

namespace App\Action\Log;

use App\Action\Action;
use App\Action\ActionInterface;
use Nyholm\Psr7\Response;

final class ViewDoctrineLog extends Action implements ActionInterface
{
    public function action(): Response
    {
        if('POST' === $this->request->getMethod()) {
            unlink("../logs/doctrine_db.log");
            $this->addSuccessMessage("Log file has been cleared");
            return $this->redirectFor('log.doctrine');
        }
        $search = $_GET['search'] ?? false;
        $lines = [];
        foreach(file("../logs/doctrine_db.log") as $line) {
            if($search) {
                if(str_contains($line, $search)) {
                    $line = json_decode($line);
                }
            } else {
                $line = json_decode($line);
            }
            foreach($line->context->trace as &$t) {
                if(str_starts_with($t->class, "App\\")) {
                    $t->ignore = true;
                } else {
                    $t->ignore = false;
                }
            }
            $lines[] = $line;
        }
        $lines = array_reverse($lines);
        return $this->render('log/viewlog.html.twig', [
            'lines' => $lines,
            'url' => 'log.doctrine'
        ]);
    }
}
