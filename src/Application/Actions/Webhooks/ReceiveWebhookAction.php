<?php
declare(strict_types=1);

namespace App\Application\Actions\Webhooks;

use Nette\Neon\Exception;
use Nette\Neon\Neon;
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
        try {
            $endpoints = Neon::decode(@file_get_contents('../endpoints.neon'))['endpoints'];
        } catch (Exception $e) {
            $this->logger->error('Syntax error in config file: ' . $e->getMessage());
            return $this->respond(new ActionPayload(500, 'Error, see log for details'));
        }

        foreach ($endpoints as $endpoint) {
            $uri = new Uri($endpoint['scheme'],
                $endpoint['host'],
                array_key_exists('port', $endpoint) ? $endpoint['port'] : null,
                $endpoint['path'],
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
