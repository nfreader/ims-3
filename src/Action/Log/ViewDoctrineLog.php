<?php

namespace App\Action\Log;

use App\Action\Action;
use Nyholm\Psr7\Response;

final class ViewDoctrineLog extends Action
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
                    $lines[] = json_decode($line);
                }
            } else {
                $lines[] = json_decode($line);
            }
        }
        $lines = array_reverse($lines);
        return $this->render('log/viewlog.html.twig', [
            'lines' => $lines,
            'url' => 'log.doctrine'
        ]);
    }
}