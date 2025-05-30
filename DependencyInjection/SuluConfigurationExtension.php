<?php

declare(strict_types=1);

namespace PatLabs\SuluConfigurationBundle\DependencyInjection;

use PatLabs\SuluConfigurationBundle\Entity\Config;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class SuluConfigurationExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasExtension('sulu_admin')) {
            $container->prependExtensionConfig(
                'sulu_admin',
                [
                    'resources' => [
                        Config::RESOURCE_KEY => [
                            'routes' => [
                                'detail' => 'config.get_config',
                            ],
                        ],
                    ],
                ],
            );
        }
    }

    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $forms = $container->getParameter('sulu_admin.forms.directories');
        if ($forms) {
            $container->setParameter('sulu_admin.forms.directories', \array_merge($forms, $config['configurations']['directories']));
        }

        $container->setParameter($this->getAlias() . '.configurations.directories', $config['configurations']['directories']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
    }
}
