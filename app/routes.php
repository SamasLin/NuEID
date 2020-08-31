<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        $basicHeaders = [
            'Origin',
            'Content-Type',
            'X-Lang'
        ];
        $authHeaders = [
            // add customized header here
        ];
        $allowHeaders = array_unique(
            array_merge(
                $basicHeaders,
                $authHeaders
            )
        );
        $response->withHeader('Access-Control-Allow-Origin', '*')
                 ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                 ->withHeader('Access-Control-Allow-Headers', implode(',', $allowHeaders))
                 ->withHeader('Access-Control-Max-Age', 86400);
        return $response;
    });

    $actionNamespace = 'App\\Application\\Actions\\';

    $app->get('/', $actionNamespace . 'User\\' . ListUserAction::class);

    $app->group('/user', function (Group $group) use ($actionNamespace) {
        $group->post('/new', $actionNamespace . 'User\\' . CreateUserAction::class);
        $group->get('/{id}', $actionNamespace . 'User\\' .  ViewUserAction::class);
        $group->put('/{id}', $actionNamespace . 'User\\' . UpdateUserAction::class);
        $group->delete('/{id}', $actionNamespace . 'User\\' . DeleteUserAction::class);
    });
};
