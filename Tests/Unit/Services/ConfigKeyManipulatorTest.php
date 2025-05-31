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

namespace PatLabs\SuluConfigurationBundle\Tests\Unit\Services;

use PatLabs\SuluConfigurationBundle\Services\ConfigKeyManipulator;
use PHPUnit\Framework\TestCase;

class ConfigKeyManipulatorTest extends TestCase
{
    private ConfigKeyManipulator $configKeyManipulator;

    protected function setUp(): void
    {
        $this->configKeyManipulator = new ConfigKeyManipulator();
    }

    public function testPrefixArrayKeys(): void
    {
        $inputArray = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        ];

        $prefix = 'test';
        $expectedResult = [
            'test.key1' => 'value1',
            'test.key2' => 'value2',
            'test.key3' => 'value3',
        ];

        $result = $this->configKeyManipulator->prefixArrayKeys($inputArray, $prefix);
        $this->assertSame($expectedResult, $result);
    }

    public function testPrefixArrayKeysWithEmptyArray(): void
    {
        $inputArray = [];
        $prefix = 'test';
        $expectedResult = [];

        $result = $this->configKeyManipulator->prefixArrayKeys($inputArray, $prefix);
        $this->assertSame($expectedResult, $result);
    }

    public function testPrefixArrayKeysWithPrefixAlreadyHavingDot(): void
    {
        $inputArray = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $prefix = 'test.';
        $expectedResult = [
            'test.key1' => 'value1',
            'test.key2' => 'value2',
        ];

        $result = $this->configKeyManipulator->prefixArrayKeys($inputArray, $prefix);
        $this->assertSame($expectedResult, $result);
    }

    public function testRemovePrefixFromArrayKeys(): void
    {
        $inputArray = [
            'test.key1' => 'value1',
            'test.key2' => 'value2',
            'test.key3' => 'value3',
            'otherprefix.key4' => 'value4',
        ];

        $prefix = 'test';
        $expectedResult = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        ];

        $result = $this->configKeyManipulator->removePrefixFromArrayKeys($inputArray, $prefix);
        $this->assertSame($expectedResult, $result);
    }

    public function testRemovePrefixFromArrayKeysWithEmptyArray(): void
    {
        $inputArray = [];
        $prefix = 'test';
        $expectedResult = [];

        $result = $this->configKeyManipulator->removePrefixFromArrayKeys($inputArray, $prefix);
        $this->assertSame($expectedResult, $result);
    }

    public function testRemovePrefixFromArrayKeysWithPrefixAlreadyHavingDot(): void
    {
        $inputArray = [
            'test.key1' => 'value1',
            'test.key2' => 'value2',
            'otherprefix.key3' => 'value3',
        ];

        $prefix = 'test.';
        $expectedResult = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $result = $this->configKeyManipulator->removePrefixFromArrayKeys($inputArray, $prefix);
        $this->assertSame($expectedResult, $result);
    }

    public function testRemovePrefixFromArrayKeysWithNonMatchingPrefix(): void
    {
        $inputArray = [
            'other.key1' => 'value1',
            'another.key2' => 'value2',
        ];

        $prefix = 'test';
        $expectedResult = [];

        $result = $this->configKeyManipulator->removePrefixFromArrayKeys($inputArray, $prefix);
        $this->assertSame($expectedResult, $result);
    }
}
