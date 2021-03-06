<?php
declare(strict_types=1);

use App\Application\Config\ConfigProvider;
use App\Application\Config\ConfigProviderInterface;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use GuzzleHttp\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
    ]);

    $containerBuilder->addDefinitions([
        ClientInterface::class => function (ContainerInterface $c) {
             return new Client(['timeout' => 8]);
        }
    ]);

    $containerBuilder->addDefinitions([
        ConfigProviderInterface::class => function (ContainerInterface $c) {
            return new ConfigProvider($c);
        }
    ]);

};
