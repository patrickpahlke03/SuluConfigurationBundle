<?php

declare(strict_types=1);

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