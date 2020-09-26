# Shlink installer

A PHP command line tool used to install [shlink](https://shlink.io/).

[![Build Status](https://img.shields.io/travis/shlinkio/shlink-installer.svg?style=flat-square)](https://travis-ci.org/shlinkio/shlink-installer)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/shlinkio/shlink-installer.svg?style=flat-square)](https://scrutinizer-ci.com/g/shlinkio/shlink-installer/)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/shlinkio/shlink-installer.svg?style=flat-square)](https://scrutinizer-ci.com/g/shlinkio/shlink-installer/)
[![Latest Stable Version](https://img.shields.io/github/release/shlinkio/shlink-installer.svg?style=flat-square)](https://packagist.org/packages/shlinkio/shlink-installer)
[![License](https://img.shields.io/github/license/shlinkio/shlink-installer.svg?style=flat-square)](https://github.com/shlinkio/shlink-installer/blob/main/LICENSE)
[![Paypal donate](https://img.shields.io/badge/Donate-paypal-blue.svg?style=flat-square&logo=paypal&colorA=aaaaaa)](https://slnk.to/donate)

![Shlink installer](shlink-installer.gif)

## Installation

Install this tool using [composer](https://getcomposer.org/).

    composer install shlinkio/shlink-installer

## Usage

This is the command line tool used by [shlink](https://github.com/shlinkio/shlink) to guide you through the installation process.

The tool expects the active directory to be a valid shlink instance.

There are two main ways to run this tool:

* Using the built-in CLI entry points.

    Run either `vendor/bin/shlink-install` or `vendor/bin/shlink-update` in order to install or update a shlink instance.

* Using the `bin/run.php` helper script.

    This script returns two functions that can be used to either install or update a shlink instance.

    Just require it and invoke the appropriate function:

    ```php
    <?php

    declare(strict_types=1);

    [$install, $update] = require 'vendor/shlinkio/shlink-installer/bin/run.php';
    $install(); // To install
    $update(); // To update
    ```

## Customize options

### Questions to ask the user

In order to retain backwards compatibility, it is possible to configure the installer to ask just a specific subset of questions.

Add a configuration file including a configuration like this:

```php
<?php

declare(strict_types=1);

use Shlinkio\Shlink\Installer\Config\Option;

return [

    'installer' => [
        'enabled_options' => [
            Option\Database\DatabaseDriverConfigOption::class,
            Option\Database\DatabaseHostConfigOption::class,
            Option\BasePathConfigOption::class,
            Option\Redirect\Regular404RedirectConfigOption::class,
            Option\UrlShortener\ShortDomainHostConfigOption::class,
            Option\UrlShortener\ShortDomainSchemaConfigOption::class,
        ],
    ],

];
```

> If `installer.enabled_options` is not provided at all, all the config options will be asked.

### Commands to run after installation

After the user has been asked for all the config, the installer will run a set of scripts which will create/update the database, download assets, etc.

It is possible to overwrite those commands via configuration too, using a syntax like this:

```php
<?php

declare(strict_types=1);

return [

    'installer' => [
        'installation_commands' => [
            'db_create_schema' => [
                'command' => 'bin/shlink shlink:db:create',
            ],
            'db_migrate' => [
                'command' => 'bin/some-script some:command',
            ],
            'orm_proxies' => [
                'command' => '-v', // Just print PHP version
            ],
        ],
    ],

];
```

This example shows all the currently available commands. They are run in the order they have been set here. 

> **Important:** Take into consideration that all the commands must be PHP scripts, since the installer will prefix all of them with the php binary. 
