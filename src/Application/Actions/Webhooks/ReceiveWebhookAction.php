<?php
declare(strict_types=1);

namespace App\Application\Actions\Webhooks;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\ActionPayload as ActionPayload;
use Slim\Psr7\Headers;
use Slim\Psr7\Request;
use Slim\Psr7\Uri;

class ReceiveWebhookAction extends WebhookAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        foreach ($this->configProvider->getEndpoints() as $endpoint) {
            if ($endpoint['preserve_path'] && isset($this->args['params'])) {
                $path = $endpoint['path'] . '/' . $this->args['params'];
            } else {
                $path = $endpoint['path'];
            }

            $uri = new Uri($endpoint['scheme'],
                $endpoint['host'],
                array_key_exists('port', $endpoint) ? $endpoint['port'] : null,
                $path,
                $endpoint['preserve_query'] ? $this->request->getUri()->getQuery() : ''
                // todo: add auth
            );

            $headers = new Headers(array_key_exists('headers', $endpoint) ? $endpoint['headers'] : []);
            foreach ($this->request->getHeaders() as $name => $value) {
                $headers->setHeader($name, $value);
            }

            $request = new Request($endpoint['method'], $uri, $headers, [], [], $this->request->getBody());
            try {
                $this->logger->info('Forwarding webhook to ' . $uri . '.');
                $forwardResponse = $this->client->sendRequest($request);
                $this->logger->info('Status: ' . $forwardResponse->getStatusCode());
            } catch (ClientExceptionInterface $e) {
                $this->logger->error('Forwarding of webhook to ' . $endpoint['host'] . ' failed. Message: ' . $e->getMessage());
            }
        }

        return $this->respond(new ActionPayload(200, "kthxbye"));
    }
}
