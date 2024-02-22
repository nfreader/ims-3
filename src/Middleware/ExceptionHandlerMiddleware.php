<?php

namespace App\Middleware;

use App\Exception\ValidationException;
use App\Renderer\JsonRenderer;
use DomainException;
use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpException;
use Slim\Views\Twig;
use Throwable;

class ExceptionHandlerMiddleware implements MiddlewareInterface
{
    private ?LoggerInterface $logger;

    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private JsonRenderer $jsonRenderer,
        private Twig $twig,
        // LoggerInterface $logger = null,
        private bool $displayErrorDetails = false,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $exception) {
            return $this->render($exception, $request);
        }
    }

    private function render(
        Throwable $exception,
        ServerRequestInterface $request,
    ): ResponseInterface {
        $httpStatusCode = $this->getHttpStatusCode($exception);
        $response = $this->responseFactory->createResponse($httpStatusCode);
        // Log error
        // if (isset($this->logger)) {
        //     $this->logger->error(
        //         sprintf(
        //             '%s;Code %s;File: %s;Line: %s',
        //             $exception->getMessage(),
        //             $exception->getCode(),
        //             $exception->getFile(),
        //             $exception->getLine()
        //         ),
        //         $exception->getTrace()
        //     );
        // }

        // Content negotiation
        if (str_contains($request->getHeaderLine('Accept'), 'application/json')) {
            $response = $response->withAddedHeader('Content-Type', 'application/json');

            // JSON
            return $this->renderJson($exception, $response);
        }

        // HTML
        return $this->renderHtml($exception, $response);
    }

    private function getHttpStatusCode(Throwable $exception): int
    {
        $statusCode = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
        }

        if ($exception instanceof DomainException || $exception instanceof InvalidArgumentException) {
            $statusCode = StatusCodeInterface::STATUS_BAD_REQUEST;
        }

        return $statusCode;
    }

    private function renderJson(Throwable $exception, ResponseInterface $response): ResponseInterface
    {
        $data = [
            'error' => [
                'message' => $exception->getMessage()
                ]
            ];
        if($exception instanceof ValidationException) {
            $data['errors'] = $exception->getErrors();
        }
        if($this->displayErrorDetails) {
            $data['error']['details'] = $exception->getTrace();
        }
        return $this->jsonRenderer->json($response, $data);
    }

    private function renderHtml(Throwable $exception, ResponseInterface $response): ResponseInterface
    {
        $type = explode('\\', get_class($exception));
        $type = end($type);
        return $this->twig->render($response, 'error.html.twig', [
            'type' => $type,
            'error' => $exception
        ]);
    }
}
