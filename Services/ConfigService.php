<?php

declare(strict_types=1);

namespace Patt\SuluConfigurationBundle\Services;

use Patt\SuluConfigurationBundle\Repository\ConfigRepository;

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
