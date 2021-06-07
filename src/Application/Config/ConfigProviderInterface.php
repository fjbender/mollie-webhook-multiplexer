<?php
declare(strict_types=1);

namespace App\Application\Config;

interface ConfigProviderInterface
{
    public function getEndpoints(): array;
}