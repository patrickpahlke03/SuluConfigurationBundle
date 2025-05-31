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

namespace PatLabs\SuluConfigurationBundle\Tests\Unit\Entity;

use PatLabs\SuluConfigurationBundle\Entity\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private Config $config;

    protected function setUp(): void
    {
        $this->config = new Config();
    }

    public function testResourceKeyConstant(): void
    {
        $this->assertSame('config', Config::RESOURCE_KEY);
    }

    public function testIdGetterAndSetter(): void
    {
        $id = 123;
        $this->config->setId($id);

        $this->assertSame($id, $this->config->getId());
    }

    public function testConfigKeyGetterAndSetter(): void
    {
        $configKey = 'app_settings.title';
        $result = $this->config->setConfigKey($configKey);

        $this->assertSame($configKey, $this->config->getConfigKey());
        $this->assertSame($this->config, $result, 'setConfigKey should return $this for fluent interface');
    }

    public function testValueGetterAndSetter(): void
    {
        $stringValue = 'test value';
        $result = $this->config->setValue($stringValue);

        $this->assertSame($stringValue, $this->config->getValue());
        $this->assertSame($this->config, $result, 'setValue should return $this for fluent interface');

        $arrayValue = ['key' => 'value'];
        $this->config->setValue($arrayValue);

        $this->assertSame($arrayValue, $this->config->getValue());

        $this->config->setValue(null);

        $this->assertNull($this->config->getValue());

        $this->config->setValue(true);

        $this->assertTrue($this->config->getValue());

        $intValue = 42;
        $this->config->setValue($intValue);

        $this->assertSame($intValue, $this->config->getValue());
    }

    public function testFluentInterface(): void
    {
        $configKey = 'app_settings.title';
        $value = 'My Website';

        $result = $this->config
            ->setConfigKey($configKey)
            ->setValue($value);

        $this->assertSame($this->config, $result);
        $this->assertSame($configKey, $this->config->getConfigKey());
        $this->assertSame($value, $this->config->getValue());
    }
}
