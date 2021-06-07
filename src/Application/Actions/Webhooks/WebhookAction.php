<?php
declare(strict_types=1);

namespace App\Application\Actions\Webhooks;

use App\Application\Actions\Action;
use App\Application\Config\ConfigProviderInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

abstract class WebhookAction extends Action
{
    /**
     * @var ClientInterface
     */
    protected $client;
    /**
     * @var ConfigProviderInterface
     */
    protected $configProvider;

    /**
     * @param LoggerInterface $logger
     * @param ClientInterface $client
     * @param ConfigProviderInterface $configProvider
     */
    public function __construct(LoggerInterface $logger, ClientInterface $client, ConfigProviderInterface $configProvider)
    {
        $this->client = $client;
        $this->configProvider = $configProvider;
        parent::__construct($logger);
    }
}
