<?php

namespace App\Domain\Incident\Middleware;

use App\Domain\Incident\Service\FetchIncidentService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Routing\RouteContext;

class GetIncidentMiddleware
{
    public function __construct(
        private FetchIncidentService $incidentService
    ) {

    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $context = RouteContext::fromRequest($request);
        $route = $context->getRoute();
        $incident = $this->incidentService->getIncident($route->getArgument('incident', null));
        $request->withAttribute('incident', $incident);
        return $handler->handle($request);
    }

}
