# Shlink installer

A PHP command line tool used to install [shlink](https://shlink.io/).

[![Build Status](https://img.shields.io/travis/shlinkio/shlink-installer.svg?style=flat-square)](https://travis-ci.org/shlinkio/shlink-installer)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/shlinkio/shlink-installer.svg?style=flat-square)](https://scrutinizer-ci.com/g/shlinkio/shlink-installer/?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/shlinkio/shlink-installer.svg?style=flat-square)](https://scrutinizer-ci.com/g/shlinkio/shlink-installer/?branch=master)
[![Latest Stable Version](https://img.shields.io/github/release/shlinkio/shlink-installer.svg?style=flat-square)](https://packagist.org/packages/shlinkio/shlink-installer)
[![License](https://img.shields.io/github/license/shlinkio/shlink-installer.svg?style=flat-square)](https://github.com/shlinkio/shlink-installer/blob/master/LICENSE)
[![Paypal donate](https://img.shields.io/badge/Donate-paypal-blue.svg?style=flat-square&logo=paypal&colorA=aaaaaa)](https://acel.me/donate)

### Installation

Install this tool using [composer](https://getcomposer.org/).

    composer install shlinkio/shlink-installer

### Usage

This is the command line tool used by [shlink](https://github.com/shlinkio/shlink) to guide you through the installation process.

The tool expects the active directory to be a valid shlink instance.

There are two main ways to run this tool:

* Using the built-in CLI entry points.

    Run either `vendor/bin/shlink-install` or `vendor/bin/shlink-update` in order to install or update a shlink instance.

* Using the `bin/run.php` helper script.

    This script returns a function that can be used to either install or update a shlink instance.

    Just require it and invoke the function:

    ```php
    <?php
    declare(strict_types=1);

    $run = require 'vendor/shlinkio/shlink-installer/bin/run.php';

    // The flag determines if we are running an update or not
    $run(false); // To install
    $run(true); // To update
    ```

### Customize options

In order to retain backwards compatibility, it is possible to configure the installer to ask just a specific subset of questions.

Add a configuration file including a configuration like this:

```php
<?php
declare(strict_types=1);

use Shlinkio\Shlink\Installer\Config\Plugin;

return [

    'installer_plugins_expected_config' => [
        Plugin\LanguageConfigCustomizer::class => [
            Plugin\LanguageConfigCustomizer::DEFAULT_LANG,
        ],

        Plugin\UrlShortenerConfigCustomizer::class => [
            Plugin\UrlShortenerConfigCustomizer::CHARS,
            Plugin\UrlShortenerConfigCustomizer::HOSTNAME,
            Plugin\UrlShortenerConfigCustomizer::SCHEMA,
        ],

        Plugin\ApplicationConfigCustomizer::class => [
            Plugin\ApplicationConfigCustomizer::DISABLE_TRACK_PARAM,
            Plugin\ApplicationConfigCustomizer::CHECK_VISITS_THRESHOLD,
        ],

        Plugin\DatabaseConfigCustomizer::class => [
            Plugin\DatabaseConfigCustomizer::DRIVER,
            Plugin\DatabaseConfigCustomizer::HOST,
            Plugin\DatabaseConfigCustomizer::PASSWORD,
        ],
    ],

];
```

By default, the installer will configure all available options for any plugin which is not provided.
