<?php

declare(strict_types=1);

namespace Patt\SuluConfigurationBundle\Entity;

class Config
{
    public const RESOURCE_KEY = 'config';

    private int $id;

    private string $configKey;

    private mixed $value = ['_value' => null];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getConfigKey(): string
    {
        return $this->configKey;
    }

    public function setConfigKey(string $configKey): self
    {
        $this->configKey = $configKey;
        return $this;
    }

    public function getValue(): mixed
    {
        return $this->value['_value'];
    }

    public function setValue(mixed $value): self
    {
        $this->value['_value'] = $value;
        return $this;
    }
}
