# SuluConfigurationBundle

**SuluConfigurationBundle** enables easy creation, management and use of custom configurations in [Sulu CMS](https://sulu.io).
It provides a flexible structure for defining and maintaining settings via the admin interface or programmatically via Symfony.

## 📦 Requirements

* Sulu CMS >= 2.6

## 🚀 Features

* Configuration management via the Sulu admin UI
* Support for all Sulu form fields
* Ability to group and structure settings
* Integration in Twig and controllers via simple service access

## 🛠️ Installation

```shell script
composer require patlabs/sulu-configuration-bundle
```

Then register the bundle:

```php
// config/bundles.php
return [
    // ...
    PatLabs\SuluConfigurationBundle\SuluConfigurationBundle::class => ['all' => true],
];
```

Add the admin routing configuration:

```yaml
# config/routes/sulu_admin.yaml
...

sulu_config_api:
  resource: "@SuluConfigurationBundle/Resources/config/routing_api.yml"
  type: rest
  prefix: /admin/api

```

Add the following package configuration:

```yaml
# config/packages/sulu_configuration.yaml
sulu_configuration:
  configurations:
    directories:
      - '%kernel.project_dir%/config/configs'
```

Create the configs folder: `config/configs`

In this folder, all desired configurations can be defined. To do this, simply create an XML that defines an Admin Form, as you already know. (https://docs.sulu.io/en/2.6/book/extend-admin.html#form-configuration)

Last but not least, you need to update the database schema:

```shell script
php bin/console doctrine:schema:update --force
```

## 🛠️ Configuration

To add a custom settings panel in the administration, simply create an XML in `config/configs` that defines an Admin Form, as you already know. (https://docs.sulu.io/en/2.6/book/extend-admin.html#form-configuration)
Also have a look at the example. (https://github.com/patrickpahlke03/SuluConfigurationBundle/tree/main/Resources/example)

## 🔤️ Admin UI

The bundle integrates seamlessly into the Sulu Admin Interface and allows:

* Editing of all defined configurations
* Validation of inputs

## 💡 Usage in Code

```php
// Controller
$siteTitle = $this->get(\PatLabs\SuluConfigurationBundle\Services\ConfigService::class)->getConfig('configKey.fieldName');
```

Or in Twig:

```twig
{{ sulu_config('configKey.fieldName') }}
```

---

**Made with ❤️ for Sulu Developers**