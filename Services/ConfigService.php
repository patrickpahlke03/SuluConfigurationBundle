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

namespace PatLabs\SuluConfigurationBundle\Services;

use PatLabs\SuluConfigurationBundle\Repository\ConfigRepository;

class ConfigService
{
    public function __construct(
        private readonly DefaultConfigProvider $defaultConfigProvider,
        private readonly ConfigRepository $configRepository,
    ) {
    }

    public function getConfig(string $key)
    {
        return $this->configRepository->findByKey($key);
    }

    public function getConfigsByPrefix(string $prefix): array
    {
        $configs = $this->configRepository->findByPrefix($prefix);
        $defaultValues = $this->defaultConfigProvider->getDefaultConfig($prefix);

        return \array_merge($defaultValues, $this->buildConfigArray($configs));
    }

    public function saveConfigs(array $configs): void
    {
        $keysExists = $this->configRepository->findByKeys(\array_keys($configs));

        foreach ($configs as $key => $value) {
            if (\array_key_exists($key, $keysExists)) {
                $this->configRepository->updateConfig($keysExists[$key]['id'], $value);
            } else {
                $this->configRepository->create($key, $value);
            }
        }

        $this->configRepository->save();
    }

    private function buildConfigArray(array $configs): array
    {
        $result = [];

        foreach ($configs as $config) {
            $result[$config['configKey']] = $config['value']['_value'];
        }

        return $result;
    }
}
