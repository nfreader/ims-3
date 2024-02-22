<?php

namespace App\Handler;

use App\Factory\LoggerFactory;
use App\Renderer\JsonRenderer;
use App\Renderer\TwigRenderer;
use DomainException;
use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpException;
use Slim\Interfaces\ErrorHandlerInterface;
use Slim\Views\Twig;
use Throwable;

/**
 * Default Error Renderer.
 */
final class DefaultErrorHandler implements ErrorHandlerInterface
{
    private ResponseFactoryInterface $responseFactory;
    private LoggerFactory $logger;
    private Twig $twig;

    public function __construct(
        private ContainerInterface $container
    ) {
        $this->responseFactory = $container->get(ResponseFactoryInterface::class);
        $this->logger = $container->get(LoggerFactory::class);
        $this->twig = $container->get(Twig::class);
    }

    /**
     * Invoke.
     *
     * @param ServerRequestInterface $request The request
     * @param Throwable $exception The exception
     * @param bool $displayErrorDetails Show error details
     * @param bool $logErrors Log errors
     * @param bool $logErrorDetails Log error details
     *
     * @return ResponseInterface The response
     */
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {
        $type = explode('\\', get_class($exception));
        $error = $this->getErrorDetails($exception, $displayErrorDetails);
        $error['request_id'] = $this->container->get('request_id');
        $error['method'] = $request->getMethod();
        $error['url'] = (string) $request->getUri();
        $error['type'] = $type;
        $response = $this->responseFactory->createResponse();
        if($logErrors) {
            $logger = $this->logger->createLogger('statbus');
            $logger->error($exception->getMessage(), $error);
        }
        $response->withHeader('Content-Type', 'application/json');
        // Render response
        // $response = $this->twig->render($response, 'error.html.twig', [
        //     'error' => $error,
        //     'display_error_details' => $displayErrorDetails,
        // ]);
        $response->withStatus($this->getHttpStatusCode($exception));
        $response->getBody()->write(
            (string) json_encode(
                [
                    'error' => $error,
                    'display_error_details' => $displayErrorDetails,
                ],
                JSON_PRETTY_PRINT
            )
        );
        return $response;
    }

    /**
     * Get http status code.
     *
     * @param Throwable $exception The exception
     *
     * @return int The http code
     */
    private function getHttpStatusCode(Throwable $exception): int
    {
        // Detect status code
        $statusCode = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
        }

        if ($exception instanceof DomainException || $exception instanceof InvalidArgumentException) {
            // Bad request
            $statusCode = StatusCodeInterface::STATUS_BAD_REQUEST;
        }

        $file = basename($exception->getFile());
        if ($file === 'CallableResolver.php') {
            $statusCode = StatusCodeInterface::STATUS_NOT_FOUND;
        }
        return $statusCode;
    }

    /**
     * Get error details.
     *
     * @param Throwable $exception The error
     * @param bool $displayErrorDetails Display details
     *
     * @return array The error details
     */
    private function getErrorDetails(Throwable $exception, bool $displayErrorDetails): array
    {
        if ($displayErrorDetails === true) {
            return [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'previous' => $exception->getPrevious(),
                'trace' => $exception->getTrace(),
            ];
        }

        return [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
        ];
    }
}
