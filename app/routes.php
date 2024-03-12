<?php

// Define app routes

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->get('/', \App\Action\Home\HomeAction::class)->setName('home');
    $app->group('/user', function (RouteCollectorProxy $app) {
        $app->post('/register', \App\Action\User\Auth\RegisterUserAction::class)->setName('user.register');
        $app->post('/login', \App\Action\User\Auth\LoginUserAction::class)->setName('user.login');
        $app->post('/logout', \App\Action\User\Auth\LogoutUserAction::class)->setName('user.logout');
        $app->post('/pickRole', \App\Action\User\SetActiveRoleAction::class);
        $app->post('/sudo', \App\Action\User\ToggleSudoModeAction::class)->setName('user.sudo');
    });

    $app->group('/incident', function (RouteCollectorProxy $app) {

        $app->get('/listing', \App\Action\Incident\ListIncidentsAction::class)->setName('incident.list');

        $app->post('/new', \App\Action\Incident\NewIncidentAction::class)->setName('incident.new');

        $app->get('/{incident:[0-9]+}', \App\Action\Incident\ViewIncidentAction::class)->setName('incident.view');

        $app->map(['GET', 'POST'], '/{incident:[0-9]+}/settings[/{setting:[a-z]+}]', \App\Action\Incident\UpdateIncidentSettingsAction::class)->setName('incident.settings');

        $app->group('/{incident:[0-9]+}/event', function (RouteCollectorProxy $app) {
            $app->get('/{event:[0-9]+}', \App\Action\Event\ViewEventAction::class)->setName('event.view');

            $app->post('/new', \App\Action\Event\NewEventAction::class)->setName('event.new');

            $app->post('/comment', \App\Action\Comment\NewCommentAction::class)->setName('comment.new');
        });

    })->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
        $request = $request->withAttribute('user', true);
        $response = $handler->handle($request);
        return $response;
    });

    $app->group('/attachment', function (RouteCollectorProxy $app) {
        $app->post('/new', \App\Action\Attachment\NewAttachmentAction::class)->setName('attachment.new');
    })->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
        $request = $request->withAttribute('user', true);
        $response = $handler->handle($request);
        return $response;
    });

    $app->group('/comment', function (RouteCollectorProxy $app) {
        $app->post('/{comment:[0-9]+}/edit', \App\Action\Comment\EditCommentAction::class)->setName('comment.edit');
    })->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
        $request = $request->withAttribute('user', true);
        $response = $handler->handle($request);
        return $response;
    });

    $app->group('/manage', function (RouteCollectorProxy $app) {
        $app->get('', \App\Action\Manage\ManageHomeAction::class)->setName('manage.home');
        $app->group('/agencies', function (RouteCollectorProxy $app) {
            $app->get('', \App\Action\Agency\ListAgenciesAction::class)->setName('agencies.home');
            $app->post('/new', \App\Action\Agency\NewAgencyAction::class)->setName('agency.new');
            $app->get('/{agency:[0-9]+}', \App\Action\Agency\ViewAgencyAction::class)->setName('agency.view');
            $app->map(['GET', 'POST'], '/{agency:[0-9]+}/edit', \App\Action\Agency\EditAgencyAction::class)->setName('agency.edit');

            //Agency Roles
            $app->group('/{agency:[0-9]+}/roles', function (RouteCollectorProxy $app) {
                $app->get('', \App\Action\Role\ViewAgencyRolesAction::class)->setName('roles.view');

                $app->post('/new', \App\Action\Role\CreateRoleAction::class)->setName('role.new');

                $app->post('/update/{action:[a-z0-9]+}', \App\Action\Role\UpdateRoleAction::class)->setName('role.update');
            });

            //Single role
            $app->group('/{agency:[0-9]+}/role/{role:[0-9]+}', function (RouteCollectorProxy $app) {
                $app->get('', \App\Action\Role\ViewRoleUsersAction::class)
                    ->setName('role.view');
            });
        });

        $app->post('/role/{role:[0-9]+}/user', \App\Action\Role\UpdateUserRoleAction::class)
            ->setName('role.user');

        $app->group('/users', function (RouteCollectorProxy $app) {
            $app->get('', \App\Action\User\ListUsersAction::class)->setName('users.home');
            $app->get('/{user:[0-9]+}', \App\Action\User\ViewUserAction::class)->setName('user.view');

            $app->post('/new', \App\Action\User\CreateNewUserAction::class)->setName('user.create');

            $app->post('/{user:[0-9]+}/agencies', \App\Action\User\EditUserAgenciesAction::class)->setName('user.agencies.edit');
            $app->post('/{user:[0-9]+}/agencies/confirm', \App\Action\User\ConfirmEditUserAgenciesAction::class)->setName('user.agencies.confirm');
        });
        $app->group('/log', function (RouteCollectorProxy $app) {
            $app->get('', \App\Action\Log\ViewLogsAction::class)->setName('logs');
            $app->map(['GET', 'POST'], '/db', \App\Action\Log\ViewDBLog::class)->setName('log.db');
            $app->map(['GET', 'POST'], '/doctrine', \App\Action\Log\ViewDoctrineLog::class)->setName('log.doctrine');
        });
    })->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
        $request = $request->withAttribute('adminOnly', true);
        $response = $handler->handle($request);
        return $response;
    });
};
