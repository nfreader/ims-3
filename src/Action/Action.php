<?php

namespace App\Action;

use App\Domain\User\Data\User;
use App\Renderer\JsonRenderer;
use App\Renderer\RedirectRenderer;
use App\Renderer\TwigRenderer;
use DI\Attribute\Inject;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Routing\RouteContext;
use Symfony\Component\HttpFoundation\Session\Session;

abstract class Action
{
    protected $config = [];

    #[Inject]
    public JsonRenderer $jsonRenderer;

    #[Inject]
    public TwigRenderer $twigRenderer;

    #[Inject]
    public RedirectRenderer $redirect;

    // private ?User $user;

    private ResponseInterface $response;

    protected ServerRequestInterface $request;

    private Session $session;

    private RouteContext $route;

    private ?array $query = null;

    private ?array $args = null;

    private ?User $user;

    private array $context = [];

    public function __construct(
        protected ContainerInterface $container
    ) {
        $this->session = $this->container->get(Session::class);
        $this->user = $this->container->get(User::class);
    }

    private function setResponse(ResponseInterface $response): static
    {
        $this->response = $response;
        return $this;
    }

    protected function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args = []): ResponseInterface
    {
        $this->setResponse($response);
        $this->setRequest($request);
        $this->permissionCheck();
        $this->setQuery();
        $this->setArgs($args);
        $this->setRoute();
        $this->request->withAttribute('request_id', $this->container->get('request_id'));
        if($this instanceof GetEntitiesInterface) {
            $this->getEntities();
        }
        if($this instanceof ActionInterface) {
            return $this->action();
        }

    }

    private function setRequest(ServerRequestInterface $request): static
    {
        $this->request = $request;
        return $this;
    }

    protected function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    private function setQuery(): static
    {
        $this->query = $this->getRequest()->getQueryParams();
        return $this;
    }

    protected function getQuery(): ?array
    {
        return $this->query;
    }


    protected function getQueryPart(string $name): ?string
    {
        if(isset($this->getQuery()[$name])) {
            return $this->getQuery()[$name];
        }
        return null;
    }

    private function setArgs(array $args): static
    {
        $this->args = $args;
        return $this;
    }

    protected function getArgs(): ?array
    {
        return $this->args;
    }

    /**
     * getArg
     *
     * Get a route argument as defined in routes.php
     *
     * Returns `null` if the argument was not found
     *
     * @param string $key
     * @return mixed
     */
    protected function getArg(string $key): mixed
    {
        if(isset($this->getArgs()[$key])) {
            return $this->getArgs()[$key];
        }
        return null;
    }

    private function setRoute(): static
    {
        $this->route = RouteContext::fromRequest($this->getRequest());
        return $this;
    }

    public function getRoute(): RouteContext
    {
        return $this->route;
    }

    /**
     * json
     *
     * Returns a json_encoded response with the given $context
     *
     * @param mixed $context
     * @return ResponseInterface
     */
    protected function json(mixed $context): ResponseInterface
    {
        $response = $this->response->withHeader("Content-Type", "application/json");
        $context = [...$context, ...$this->context];
        $response->getBody()->write(json_encode($context, JSON_PRETTY_PRINT));
        return $response;
    }

    /**
     * render
     *
     * Renders the given twig $template file with the given $context to a response. If the client indicates that they want a json response, we fall back to the json method
     *
     * @param string $template
     * @param mixed $context
     * @return ResponseInterface
     */
    protected function render(string $template = 'debug.html.twig', mixed $context = []): ResponseInterface
    {
        if('application/json' === $this->request->getHeaderLine('Accept')) {
            return $this->json($context);
        }
        $context = [...$context, ...$this->context];
        return $this->twigRenderer->render($this->getResponse(), $template, $context);
    }

    /**
     * addContext
     * Add context to the renderer before the call to render() or json()
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function addContext(string $key, mixed $value): static
    {
        $this->context[$key] = $value;
        return $this;
    }

    /**
     * redirectFor
     *
     * Returns a response object with a 302 header that redirects the user to the specified route with the specified parameters.
     *
     * @param string $route
     * @param array $data
     * @param array $queryParams
     * @return ResponseInterface
     */
    public function redirectFor(string $route, array $data = [], array $queryParams = []): ResponseInterface
    {
        return $this->redirect->redirectFor($this->getResponse(), $route, $data, $queryParams);
    }

    /**
     * getUriForRoute
     *
     * Gets the full, complete URI (including protocol) for the given `$route`
     * name.
     *
     * Attempts to remove the port from the URI.
     *
     * @param string $route
     * @return string
     */
    protected function getUriForRoute(string $route, $args = []): string
    {
        $router = $this->container->get(RouteParserInterface::class);
        $uri = $router->fullUrlFor(
            $this->getRequest()->getUri()->withPort(null),
            $route,
            $args
        );
        if ((isset($_SERVER['HTTPS']) && 'On' === $_SERVER['HTTPS']) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            $uri = str_replace('http://', 'https://', $uri);
        }
        return $uri;
    }

    /**
     * addSuccessMessage
     *
     * Adds a success (green) message to the Session global's flash bag.
     *
     * @link https://symfony.com/doc/current/session.html#flash-messages
     *
     * @param string $message
     * @return self
     */
    public function addSuccessMessage(string $message): static
    {
        $this->session->getFlashbag()->add('success', $message."\nDEPRECATED\n");

        return $this;
    }

    /**
     * addMessage
     *
     * Adds a message to the Session global's flash bag.
     *
     * @link https://symfony.com/doc/current/session.html#flash-messages
     *
     * @param string $message
     * @return self
     */
    public function addMessage(string $message): static
    {
        $this->session->getFlashbag()->add('info', $message."\nDEPRECATED\n");

        return $this;
    }

    /**
     * addErrorMessage
     *
     * Adds a success (green) message to the Session global's flash bag.
     *
     * @link https://symfony.com/doc/current/session.html#flash-messages
     *
     * @param string $message
     * @return self
     */
    public function addErrorMessage(string $message): static
    {
        $this->session->getFlashbag()->add('danger', $message."\nDEPRECATED\n");
        return $this;
    }

    /**
     * getUser
     *
     * Returns an instance of the current logged in user, or null if there is
     * no logged in user.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    // private function permissionCheck(): void
    // {
    //     $user = $this->getUser();
    //     $activeUser = $this->getRequest()->getAttribute('user');
    //     if($activeUser && !$user) {
    //         throw new StatbusUnauthorizedException("You must be logged in to access this", 403);
    //     }
    //     $require = $this->getRequest()->getAttribute('require');
    //     if($require) {
    //         if($require && !$user) {
    //             $this->session->set('authRedirect', (string) $this->getRequest()->getUri()->withPort(null));
    //             throw new StatbusUnauthorizedException("You must be logged in to access this", 403);
    //         } elseif ($require && !$user->has($require)) {
    //             throw new StatbusUnauthorizedException("You do not have permission to access this", 403);
    //         }
    //     }

    // }

    public function getSession(): Session
    {
        return $this->session;
    }

    /**
     * Verify that the current logged in user has the permission required
     * by the request attribute. Throws an exception if this check fails, or
     * if the user is not logged in.
     *
     * @return void
     */
    private function permissionCheck(): void
    {
        $user = $this->getUser();
        $requireUser = $this->getRequest()->getAttribute('user');
        if($requireUser && !$user) {
            throw new Exception("You must be logged in to access this", 403);
        }
        $adminOnly = $this->getRequest()->getAttribute('adminOnly');
        if($adminOnly && !$user) {
            throw new Exception("You must be logged in to access this", 403);
        } elseif ($adminOnly && !$user->isAdmin()) {
            throw new Exception("You do not have permission to access this", 403);
        }
    }

}
