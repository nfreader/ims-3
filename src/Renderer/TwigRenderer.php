<?php

namespace App\Renderer;

use DI\Attribute\Inject;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;

final class TwigRenderer
{
    #[Inject]
    private Twig $twig;

    public function render(
        ResponseInterface $response,
        string $template,
        mixed $data = []
    ): ResponseInterface {
        return $this->twig->render($response, $template, $data);
    }
}
