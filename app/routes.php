<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->group('/webhooks[/{params:.*}]', function (Group $group) {
        $group->post('', \App\Application\Actions\Webhooks\ReceiveWebhookAction::class);
        $group->get('', \App\Application\Actions\Webhooks\ReceiveWebhookAction::class); // todo remove
    });
};
