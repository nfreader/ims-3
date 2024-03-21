<?php

namespace App\Action\Log;

use App\Action\Action;
use App\Action\ActionInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class ViewPhpStanLog extends Action implements ActionInterface
{
    public function action(): ResponseInterface
    {
        if('POST' === $this->request->getMethod()) {
            unlink(Path::normalize("../tmp/phpstan_out.json"));
            $filesystem = new Filesystem();
            $filesystem->touch("../tmp/phpstan_out.json");
            $this->addSuccessMessage("Log file has been cleared");
            return $this->redirectFor('log.phpstan');
        }

        $errors = json_decode(
            file_get_contents(Path::normalize("../tmp/phpstan_out.json")),
            true
        );
        return $this->render('log/phpstan.html.twig', [
            'errors' => $errors,
            'url' => 'log.phpstan'
        ]);
    }
}
