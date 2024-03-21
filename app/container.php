<?php

use App\Domain\User\Data\User;
use App\Domain\User\Service\RefreshUserFromSessionService;
use App\Extension\Twig\EnumExtension;
use App\Extension\Twig\WebpackAssetLoader;
use App\Factory\LoggerFactory;
use App\Handler\DefaultErrorHandler;
use App\Middleware\ExceptionHandlerMiddleware;
use App\Renderer\JsonRenderer;
use App\Repository\Repository;
use App\Repository\QueryLogger as RepositoryQueryLogger;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Embed\Embed;
use Firehed\DbalLogger\Middleware;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\Embed\Bridge\OscaroteroEmbedAdapter;
use League\CommonMark\Extension\Embed\EmbedExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\MarkdownConverter;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Nyholm\Psr7\Factory\Psr17Factory;
use ParagonIE\EasyDB\EasyDB;
use ParagonIE\EasyDB\EasyDBCache;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Interfaces\RouteParserInterface;
use Slim\Middleware\ErrorMiddleware;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Twig\Extra\Html\HtmlExtension;
use Twig\Extra\String\StringExtension;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

return [
    // Application settings
    'settings' => fn () => require Path::normalize(__DIR__ . '/settings.php'),

    'request_id' => fn () => substr(strtoupper(bin2hex(random_bytes(32))), 0, 6),

    App::class => function (ContainerInterface $container) {
        $app = AppFactory::createFromContainer($container);

        // Register routes
        (require Path::normalize(__DIR__ . '/routes.php'))($app);

        // Register middleware
        (require Path::normalize(__DIR__ . '/middleware.php'))($app);

        return $app;
    },

    // HTTP factories
    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    ServerRequestFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    StreamFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    UriFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    // The Slim RouterParser
    RouteParserInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getRouteCollector()->getRouteParser();
    },

    ErrorMiddleware::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['error'];
        $app = $container->get(App::class);

        $logger = $container->get(LoggerFactory::class)
            ->addFileHandler('error.log')
            ->createLogger();


        $errorMiddleware = new ErrorMiddleware(
            $app->getCallableResolver(),
            $app->getResponseFactory(),
            (bool)$settings['display_error_details'],
            (bool)$settings['log_errors'],
            (bool)$settings['log_error_details'],
            $logger
        );

        $errorMiddleware->setDefaultErrorHandler($container->get(DefaultErrorHandler::class));

        return $errorMiddleware;
    },

    //TwigMiddleware
    TwigMiddleware::class => function (ContainerInterface $container) {
        return TwigMiddleware::createFromContainer(
            $container->get(App::class),
            Twig::class
        );
    },

    //Twig
    Twig::class => function (ContainerInterface $container) {
        $session = $container->get(Session::class);
        $settings = $container->get("settings");
        $twigConfig = $settings['twig'];
        $appSettings = $settings['application'];
        $appSettings['user'] = $container->get(User::class);
        $twigConfig['options']['cache'] = $twigConfig['options']['cache_enabled']
            ? $twigConfig['options']['cache_path']
            : false;

        $twig = Twig::create($twigConfig['paths'], $twigConfig['options']);

        $loader = $twig->getLoader();
        $publicPath = (string) $settings['public'];
        if ($loader instanceof FilesystemLoader) {
            $loader->addPath($publicPath, 'public');
        }
        $twig->getEnvironment()->addGlobal("debug", $settings["debug"]);
        $twig->getEnvironment()->addGlobal("app", $appSettings);
        $twig->getEnvironment()->addGlobal("flash", $session->getFlashBag()->all());
        $twig->getEnvironment()->addGlobal('request_id', $container->get('request_id'));

        $twig->addExtension(new WebpackAssetLoader($settings['public'], $settings['debug']));
        $twig->addExtension(new EnumExtension());
        $twig->addExtension(new \Twig\Extension\DebugExtension());
        $twig->addExtension(new StringExtension());
        $twig->addExtension(new HtmlExtension());

        $twig->addRuntimeLoader(new class () implements RuntimeLoaderInterface {
            public function load($class)
            {
                $config = [
                    'default_attributes' => [
                        Table::class => [
                            'class' => 'table table-bordered',
                        ],
                        BlockQuote::class => [
                            'class' => 'blockquote border-start border-4 ps-4'
                        ],
                    ],
                ];
                if (MarkdownRuntime::class === $class) {
                    $embedLibrary = new Embed();
                    $embedLibrary->setSettings([
                        'oembed:query_parameters' => [
                            'maxwidth' => 800,
                            'maxheight' => 600,
                        ],
                    ]);
                    $config['embed'] = [
                        'adapter' => new OscaroteroEmbedAdapter(),
                        'allowed_domains' => ['youtube.com', 'twitter.com', 'github.com'],
                        'fallback' => 'link'
                    ];
                    $environment = new Environment($config);
                    $environment->addExtension(new CommonMarkCoreExtension());
                    $environment->addExtension(new DefaultAttributesExtension());
                    $environment->addExtension(new GithubFlavoredMarkdownExtension());
                    // $environment->addExtension(new EmbedExtension());
                    return new MarkdownConverter($environment);
                }
            }
        });
        $twig->addExtension(new \Twig\Extra\Markdown\MarkdownExtension());
        $twig->getEnvironment()->getExtension(\Twig\Extension\CoreExtension::class)->setDateFormat(
            $appSettings['date_format'],
            $appSettings['interval_format']
        );
        $twig->getEnvironment()->getExtension(\Twig\Extension\CoreExtension::class)->setTimezone($appSettings['timezone']);
        return $twig;
    },

    //Session
    Session::class => function (ContainerInterface $container) {
        $settings = $container->get("settings")["session"];
        if (PHP_SAPI === "cli") {
            return new Session(new MockArraySessionStorage());
        } else {
            return new Session(new NativeSessionStorage($settings));
        }
    },

    PDO::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['database'];
        $dsn = sprintf(
            "mysql:host=%s:%s;dbname=%s",
            $settings['host'],
            $settings['port'],
            $settings['database']
        );
        $db = new \PDO(
            $dsn,
            $settings['username'],
            $settings['password'],
        );
        return $db;
    },

    EasyDB::class => function (ContainerInterface $container) {
        try {
            return EasyDBCache::fromEasyDB(new EasyDB($container->get(PDO::class)));
        } catch (Exception $e) {
            die("The IMS database is not available.");
        }
    },

    User::class => function (ContainerInterface $container) {
        return (new RefreshUserFromSessionService(
            $container,
        ))->refreshUser();
    },

    LoggerFactory::class => function (ContainerInterface $container) {
        return new LoggerFactory($container->get('settings')['logger']);
    },

    Filesystem::class => function (ContainerInterface $container) {
        $adapter = new LocalFilesystemAdapter($container->get('settings')['upload_dir']);
        return new Filesystem($adapter);
    },

    ExceptionHandlerMiddleware::class => function (ContainerInterface $container) {
        return new ExceptionHandlerMiddleware(
            $container->get(ResponseFactoryInterface::class),
            $container->get(JsonRenderer::class),
            $container->get(Twig::class),
            // $container->get(LoggerFactory::class),
            $container->get('settings')['error']['display_error_details']
        );
    },
    Connection::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['database'];
        $params = [
            'dbname' => $settings['database'],
            'user' => $settings['username'],
            'password' => $settings['password'],
            'host' => $settings['host'],
            'port' => $settings['port'],
            'charset' => 'utf8mb4',
            'driver' => "pdo_mysql"
        ];
        $configuration = new Configuration();

        if ($settings['log_queries']) {
            $logger = $container->get(RepositoryQueryLogger::class);
            $configuration->setMiddlewares([new Middleware($logger)]);
        }
        $connection = DriverManager::getConnection($params, $configuration);

        return $connection;
    },

    RepositoryQueryLogger::class => function (ContainerInterface $container) {
        return new RepositoryQueryLogger(
            $container->get(
                LoggerFactory::class
            ),
            $container->get('request_id')
        );
    },

    Repository::class => function (ContainerInterface $container) {
        return new Repository($container->get(Connection::class));
    }
];
