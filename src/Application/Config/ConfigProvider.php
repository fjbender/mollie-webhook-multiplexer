<?php
declare(strict_types=1);

namespace App\Application\Config;

use Nette\Neon\Exception as NeonException;
use Nette\Neon\Neon;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class ConfigProvider implements ConfigProviderInterface
{
    private $endpoints;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(ContainerInterface $c)
    {
        $this->logger = $c->get(LoggerInterface::class);
        try {
            $this->endpoints = Neon::decode(@file_get_contents('../endpoints.neon'))['endpoints'];
        } catch (NeonException $e) {
            $this->logger->error('Syntax error in config file: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getEndpoints(): array
    {
        return $this->endpoints;
    }
}