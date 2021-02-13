# Shlink installer

A PHP command line tool used to install [shlink](https://shlink.io/).

[![Build Status](https://img.shields.io/github/workflow/status/shlinkio/shlink-installer/Continuous%20integration?logo=github&style=flat-square)](https://github.com/shlinkio/shlink-installer/actions?query=workflow%3A%22Continuous+integration%22)
[![Code Coverage](https://img.shields.io/codecov/c/gh/shlinkio/shlink-installer/develop?style=flat-square)](https://app.codecov.io/gh/shlinkio/shlink-installer)
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

* Using the built-in CLI entry point.

    Run `vendor/bin/shlink-installer` to print all available commands.

    ```
    Shlink installer

    Usage:
    command [options] [arguments]

    Options:
    -h, --help            Display help for the given command. When no command is given display help for the list command
    -q, --quiet           Do not output any message
    -V, --version         Display this application version
    --ansi            Force ANSI output
    --no-ansi         Disable ANSI output
    -n, --no-interaction  Do not ask any interactive question
    -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

    Available commands:
    help        Displays help for a command
    install     Guides you through the installation process, to get Shlink up and running.
    list        Lists commands
    set-option  Allows you to set new values for any config option.
    update      Helps you import Shlink's config from an older version to a new one.
    ```

    > You can also run `vendor/bin/shlink-install` or `vendor/bin/shlink-update`, which alias the `install` and `update` commands respectively, but this is deprecated and will be removed in next major release.

* Using the `bin/run.php` helper script.

    This script returns three functions that can be used to run the install or update, or the whole shlink installer tool.

    Just require it and invoke the appropriate function:

    ```php
    <?php

    declare(strict_types=1);

    [$install, $update, $installer] = require 'vendor/shlinkio/shlink-installer/bin/run.php';
    $install(); // To install
    $update(); // To update
    $installer(); // To run any supported commands
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
