<?php

declare(strict_types=1);

namespace PatLabs\SuluConfigurationBundle\Twig;

use PatLabs\SuluConfigurationBundle\Services\ConfigService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConfigExtension extends AbstractExtension
{
    public function __construct(private readonly ConfigService $configService)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sulu_config', [$this, 'getConfig']),
        ];
    }

    public function getConfig($key)
    {
        $config = $this->configService->getConfig($key);

        if (!$config) {
            return null;
        }

        return $config->getValue();
    }
}