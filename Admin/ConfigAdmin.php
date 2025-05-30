<?php

declare(strict_types=1);

namespace Patt\SuluConfigurationBundle\Admin;

use Patt\SuluConfigurationBundle\Entity\Config;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Bundle\AdminBundle\Exception\NavigationItemNotFoundException;
use Sulu\Bundle\AdminBundle\FormMetadata\FormXmlLoader;
use Symfony\Component\Finder\Finder;

class ConfigAdmin extends Admin
{
    private array $configurationKeys = [];

    public function __construct(
        private readonly ViewBuilderFactoryInterface $viewBuilderFactory,
        private readonly FormXmlLoader $formXmlLoader,
        private readonly array $configurationDirectories,
    ) {
        $configurationFinder = (new Finder())->in($this->configurationDirectories)->name('*.xml');

        foreach ($configurationFinder as $configurationFile) {
            $formMetadataCollection = $this->formXmlLoader->load($configurationFile->getPathName());
            $items = $formMetadataCollection->getItems();
            $this->configurationKeys[] = \reset($items)->getKey();
        }
    }

    /**
     * @throws NavigationItemNotFoundException
     */
    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        $settingsNavigationItem = $navigationItemCollection->get(Admin::SETTINGS_NAVIGATION_ITEM);

        foreach ($this->configurationKeys as $configurationKey) {
            if (\str_contains($configurationKey, '.')) {
                continue;
            }

            $customSetting = new NavigationItem('sulu_configuration.' . $configurationKey . '.navigation_name');
            $customSetting->setPosition(100);
            $customSetting->setView($configurationKey);

            $settingsNavigationItem->addChild($customSetting);
        }
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        foreach ($this->configurationKeys as $configurationKey) {
            $keys = \explode('.', $configurationKey);
            $key = $keys[0];
            $tab = 'general';
            $tabOrder = 0;

            if (\count($keys) > 1) {
                $tab = $keys['1'];
                $tabOrder = 1;
            } else {
                $viewCollection->add(
                    $this->viewBuilderFactory->createResourceTabViewBuilder($configurationKey, '/configuration/' . $configurationKey)
                        ->setResourceKey(Config::RESOURCE_KEY)
                        ->setAttributeDefault('id', $configurationKey),
                );
            }

            $viewCollection->add(
                $this->viewBuilderFactory->createFormViewBuilder('sulu_configuration.' . $key . '.' . $tab, '/form')
                    ->setResourceKey(Config::RESOURCE_KEY)
                    ->addToolbarActions([
                        new ToolbarAction('sulu_admin.save'),
                    ])
                    ->setTabOrder($tabOrder)
                    ->setFormKey($configurationKey)
                    ->setParent($key),
            );
        }
    }
}
