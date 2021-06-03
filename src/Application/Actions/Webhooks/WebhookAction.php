<?php
declare(strict_types=1);

namespace App\Application\Actions\Webhooks;

use App\Application\Actions\Action;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

abstract class WebhookAction extends Action
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @param LoggerInterface $logger
     * @param ClientInterface $client
     */
    public function __construct(LoggerInterface $logger, ClientInterface $client)
    {
        $this->client = $client;
        parent::__construct($logger);
    }
}
