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

class ConfigKeyManipulator
{
    public function prefixArrayKeys(array $array, string $prefix): array
    {
        $prefix = \trim($prefix, '.') . '.';

        $result = [];
        foreach ($array as $key => $value) {
            $result[$prefix . $key] = $value;
        }

        return $result;
    }

    public function removePrefixFromArrayKeys(array $array, string $prefix): array
    {
        $prefix = \trim($prefix, '.') . '.';
        $prefixLength = \strlen($prefix);

        $result = [];
        foreach ($array as $key => $value) {
            if (\str_starts_with($key, $prefix)) {
                $result[\substr($key, $prefixLength)] = $value;
            }
        }

        return $result;
    }
}
