# Shlink installer

A PHP command line tool used to install [shlink](https://shlink.io/).

[![Build Status](https://img.shields.io/github/actions/workflow/status/shlinkio/shlink-installer/ci.yml?branch=develop&logo=github&style=flat-square)](https://github.com/shlinkio/shlink-installer/actions/workflows/ci.yml?query=workflow%3A%22Continuous+integration%22)
[![Code Coverage](https://img.shields.io/codecov/c/gh/shlinkio/shlink-installer/develop?style=flat-square)](https://app.codecov.io/gh/shlinkio/shlink-installer)
[![Infection MSI](https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fshlinkio%2Fshlink-installer%2Fdevelop)](https://dashboard.stryker-mutator.io/reports/github.com/shlinkio/shlink-installer/develop)
[![Latest Stable Version](https://img.shields.io/github/release/shlinkio/shlink-installer.svg?style=flat-square)](https://packagist.org/packages/shlinkio/shlink-installer)
[![License](https://img.shields.io/github/license/shlinkio/shlink-installer.svg?style=flat-square)](https://github.com/shlinkio/shlink-installer/blob/main/LICENSE)
[![Paypal donate](https://img.shields.io/badge/Donate-paypal-blue.svg?style=flat-square&logo=paypal&colorA=aaaaaa)](https://slnk.to/donate)

![Shlink installer](shlink-installer.gif)

## Installation

Install this tool using [composer](https://getcomposer.org/).

    composer install shlinkio/shlink-installer

## Usage

This is the command line tool used by [shlink](https://github.com/shlinkio/shlink) to guide you through the installation process.

The tool expects the working directory to be a valid shlink instance.

In order to run it, use the built-in CLI entry point.

Run `vendor/bin/shlink-installer` to print all available commands.

```
Shlink installer

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display help for the given command. When no command is given display help for the list command
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi|--no-ansi  Force (or disable --no-ansi) ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  help        Display help for a command
  install     Guides you through the installation process, to get Shlink up and running.
  list        List commands
  set-option  Allows you to set new values for any config option.
  update      Helps you import Shlink's config from an older version to a new one.
```

The most important ones are these:

* `install`: Used to set up Shlink from scratch.
* `update`: Used to update an existing Shlink instance. Will allow importing the config, skipping the options that already have a value.
* `set-option`: Allows to set the value for an individual option, in case you want to update it.

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

use Shlinkio\Shlink\Installer\Util\InstallationCommand;

return [

    'installer' => [
        'installation_commands' => [
            InstallationCommand::DB_CREATE_SCHEMA->value => [
                'command' => 'bin/shlink shlink:db:create',
            ],
            InstallationCommand::DB_MIGRATE->value => [
                'command' => 'bin/some-script some:command',
            ],
            InstallationCommand::ORM_PROXIES->value => [
                'command' => '-v', // Just print PHP version
            ],
        ],
    ],

];
```

This example shows all the currently available commands. They are run in the order they have been set here. 

> **Important:** Take into consideration that all the commands must be PHP scripts, since the installer will prefix all of them with the php binary. 
