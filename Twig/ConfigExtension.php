<?php

declare(strict_types=1);

/*
 * This file is part of the SuluConfigurationBundle.
 *
 * (c) Patrick Pahlke
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
