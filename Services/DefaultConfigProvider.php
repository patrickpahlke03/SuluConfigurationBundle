<?php

declare(strict_types=1);

namespace PatLabs\SuluConfigurationBundle\Services;

use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\FieldMetadata;
use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\FormMetadata;
use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\SectionMetadata;
use Sulu\Bundle\AdminBundle\Metadata\MetadataProviderInterface;
use Sulu\Component\Security\Authentication\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DefaultConfigProvider
{
    public function __construct(
        private readonly MetadataProviderInterface $formMetadataProvider,
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    public function getDefaultConfig(string $key): array
    {
        $defaultValues = [];
        if ($this->tokenStorage && null !== $this->tokenStorage->getToken() && $this->formMetadataProvider) {
            $user = $this->tokenStorage->getToken()->getUser();

            if (!$user instanceof UserInterface) {
                return $defaultValues;
            }

            /** @var FormMetadata $metadata */
            $metadata = $this->formMetadataProvider->getMetadata($key, $user->getLocale(), []);

            $defaultValues = $this->getDefaultValues($key, $metadata->getItems());
        }

        return $defaultValues;
    }

    private function getDefaultValues(string $key, array $metadata): array
    {
        $items = [];

        foreach ($metadata as $item) {
            if ($item instanceof SectionMetadata) {
                $items = \array_merge($items, $this->getDefaultValues($key, $item->getItems()));
            } elseif ($item instanceof FieldMetadata) {
                $defaultValue = null;
                if ($default = $item->getOptions()['default_value'] ?? null) {
                    $defaultValue = $default->getValue();
                }

                $items[$key . '.' . $item->getName()] = $defaultValue;
            }
        }

        return $items;
    }
}