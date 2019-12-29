<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Model;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Plugin;
use Shlinkio\Shlink\Installer\Model\CustomizableAppConfig;

class CustomizableAppConfigTest extends TestCase
{
    /** @test */
    public function exchangeArrayIgnoresAnyNonProvidedKey(): void
    {
        $config = new CustomizableAppConfig();

        $config->exchangeArray([
            'app_options' => [
                'disable_track_param' => null,
            ],
        ]);

        $this->assertFalse($config->hasImportedInstallationPath());
        $this->assertFalse($config->hasDatabase());
        $this->assertFalse($config->hasUrlShortener());
        $this->assertFalse($config->hasRedirects());
        $this->assertTrue($config->hasApp());
        $this->assertEquals([
            Plugin\ApplicationConfigCustomizer::DISABLE_TRACK_PARAM => null,
        ], $config->getApp());
    }

    /**
     * @test
     * @dataProvider provideConfigsToParse
     */
    public function getArrayCopyParsesPlainConfigToAppExpectedStructure(array $provided, array $expected): void
    {
        $config = new CustomizableAppConfig();
        $config
            ->setApp($provided[Plugin\ApplicationConfigCustomizer::class])
            ->setDatabase($provided[Plugin\DatabaseConfigCustomizer::class])
            ->setUrlShortener($provided[Plugin\UrlShortenerConfigCustomizer::class])
            ->setRedirects($provided[Plugin\RedirectsConfigCustomizer::class]);

        $this->assertEquals($expected, $config->getArrayCopy());
    }

    public function provideConfigsToParse(): iterable
    {
        yield [[
            Plugin\ApplicationConfigCustomizer::class => [
                Plugin\ApplicationConfigCustomizer::SECRET => 'abc123',
                Plugin\ApplicationConfigCustomizer::DISABLE_TRACK_PARAM => 'foo',
                Plugin\ApplicationConfigCustomizer::CHECK_VISITS_THRESHOLD => true,
                Plugin\ApplicationConfigCustomizer::VISITS_THRESHOLD => 50,
                Plugin\ApplicationConfigCustomizer::WEB_WORKER_NUM => 64,
                Plugin\ApplicationConfigCustomizer::TASK_WORKER_NUM => 32,
            ],
            Plugin\DatabaseConfigCustomizer::class => [
                Plugin\DatabaseConfigCustomizer::DRIVER => 'pdo_mysql',
                Plugin\DatabaseConfigCustomizer::USER => '',
                Plugin\DatabaseConfigCustomizer::PASSWORD => 'foo',
                Plugin\DatabaseConfigCustomizer::NAME => '',
                Plugin\DatabaseConfigCustomizer::HOST => 'local',
                Plugin\DatabaseConfigCustomizer::PORT => '',
            ],
            Plugin\UrlShortenerConfigCustomizer::class => [
                Plugin\UrlShortenerConfigCustomizer::SCHEMA => 'http',
                Plugin\UrlShortenerConfigCustomizer::HOSTNAME => '',
                Plugin\UrlShortenerConfigCustomizer::VALIDATE_URL => true,
            ],
            Plugin\RedirectsConfigCustomizer::class => [
                Plugin\RedirectsConfigCustomizer::INVALID_SHORT_URL_REDIRECT_TO => null,
                Plugin\RedirectsConfigCustomizer::REGULAR_404_REDIRECT_TO => null,
                Plugin\RedirectsConfigCustomizer::BASE_URL_REDIRECT_TO => null,
            ],
        ], [
            'app_options' => [
                'secret_key' => 'abc123',
                'disable_track_param' => 'foo',
            ],
            'delete_short_urls' => [
                'check_visits_threshold' => true,
                'visits_threshold' => 50,
            ],
            'entity_manager' => [
                'connection' => [
                    'driver' => 'pdo_mysql',
                    'user' => '',
                    'password' => 'foo',
                    'dbname' => '',
                    'host' => 'local',
                    'port' => '',
                    'driverOptions' => [
                        1002 => 'SET NAMES utf8',
                    ],
                ],
            ],
            'url_shortener' => [
                'domain' => [
                    'schema' => 'http',
                    'hostname' => '',
                ],
                'validate_url' => true,
            ],
            'not_found_redirects' => [
                'invalid_short_url' => null,
                'regular_404' => null,
                'base_url' => null,
            ],
            'web_worker_num' => 64,
            'task_worker_num' => 32,
        ]];

        yield [[
            Plugin\ApplicationConfigCustomizer::class => [
                Plugin\ApplicationConfigCustomizer::SECRET => '',
                Plugin\ApplicationConfigCustomizer::DISABLE_TRACK_PARAM => null,
                Plugin\ApplicationConfigCustomizer::CHECK_VISITS_THRESHOLD => true,
                Plugin\ApplicationConfigCustomizer::VISITS_THRESHOLD => 15,
            ],
            Plugin\DatabaseConfigCustomizer::class => [
                Plugin\DatabaseConfigCustomizer::DRIVER => 'pdo_sqlite',
            ],
            Plugin\UrlShortenerConfigCustomizer::class => [
                Plugin\UrlShortenerConfigCustomizer::SCHEMA => 'http',
                Plugin\UrlShortenerConfigCustomizer::HOSTNAME => '',
                Plugin\UrlShortenerConfigCustomizer::VALIDATE_URL => true,
            ],
            Plugin\RedirectsConfigCustomizer::class => [
                Plugin\RedirectsConfigCustomizer::INVALID_SHORT_URL_REDIRECT_TO => null,
                Plugin\RedirectsConfigCustomizer::REGULAR_404_REDIRECT_TO => null,
                Plugin\RedirectsConfigCustomizer::BASE_URL_REDIRECT_TO => null,
            ],
        ], [
            'app_options' => [
                'secret_key' => '',
                'disable_track_param' => null,
            ],
            'delete_short_urls' => [
                'check_visits_threshold' => true,
                'visits_threshold' => 15,
            ],
            'entity_manager' => [
                'connection' => [
                    'driver' => 'pdo_sqlite',
                    'path' => 'data/database.sqlite',
                ],
            ],
            'url_shortener' => [
                'domain' => [
                    'schema' => 'http',
                    'hostname' => '',
                ],
                'validate_url' => true,
            ],
            'not_found_redirects' => [
                'invalid_short_url' => null,
                'regular_404' => null,
                'base_url' => null,
            ],
        ]];

        yield [[
            Plugin\ApplicationConfigCustomizer::class => [
                Plugin\ApplicationConfigCustomizer::SECRET => '',
                Plugin\ApplicationConfigCustomizer::DISABLE_TRACK_PARAM => null,
                Plugin\ApplicationConfigCustomizer::CHECK_VISITS_THRESHOLD => true,
                Plugin\ApplicationConfigCustomizer::VISITS_THRESHOLD => 15,
            ],
            Plugin\DatabaseConfigCustomizer::class => [
                Plugin\DatabaseConfigCustomizer::DRIVER => 'pdo_sqlite',
            ],
            Plugin\UrlShortenerConfigCustomizer::class => [
                Plugin\UrlShortenerConfigCustomizer::HOSTNAME => 'doma.in',
                Plugin\UrlShortenerConfigCustomizer::SCHEMA => 'https',
                Plugin\UrlShortenerConfigCustomizer::VALIDATE_URL => false,
            ],
            Plugin\RedirectsConfigCustomizer::class => [
                Plugin\RedirectsConfigCustomizer::INVALID_SHORT_URL_REDIRECT_TO => 'aaabbbccc',
                Plugin\RedirectsConfigCustomizer::REGULAR_404_REDIRECT_TO => null,
                Plugin\RedirectsConfigCustomizer::BASE_URL_REDIRECT_TO => 'something',
            ],
        ], [
            'app_options' => [
                'secret_key' => '',
                'disable_track_param' => null,
            ],
            'delete_short_urls' => [
                'check_visits_threshold' => true,
                'visits_threshold' => 15,
            ],
            'entity_manager' => [
                'connection' => [
                    'driver' => 'pdo_sqlite',
                    'path' => 'data/database.sqlite',
                ],
            ],
            'url_shortener' => [
                'domain' => [
                    'schema' => 'https',
                    'hostname' => 'doma.in',
                ],
                'validate_url' => false,
            ],
            'not_found_redirects' => [
                'invalid_short_url' => 'aaabbbccc',
                'regular_404' => null,
                'base_url' => 'something',
            ],
        ]];

        yield [[
            Plugin\ApplicationConfigCustomizer::class => [
                Plugin\ApplicationConfigCustomizer::CHECK_VISITS_THRESHOLD => true,
                Plugin\ApplicationConfigCustomizer::VISITS_THRESHOLD => 15,
            ],
            Plugin\DatabaseConfigCustomizer::class => [
                Plugin\DatabaseConfigCustomizer::DRIVER => 'pdo_sqlite',
            ],
            Plugin\UrlShortenerConfigCustomizer::class => [
                Plugin\UrlShortenerConfigCustomizer::SCHEMA => 'https',
                Plugin\UrlShortenerConfigCustomizer::VISITS_WEBHOOKS => ['foo', 'bar'],
            ],
            Plugin\RedirectsConfigCustomizer::class => [
                Plugin\RedirectsConfigCustomizer::REGULAR_404_REDIRECT_TO => 'aaabbbccc',
            ],
        ], [
            'delete_short_urls' => [
                'check_visits_threshold' => true,
                'visits_threshold' => 15,
            ],
            'entity_manager' => [
                'connection' => [
                    'driver' => 'pdo_sqlite',
                    'path' => 'data/database.sqlite',
                ],
            ],
            'url_shortener' => [
                'domain' => [
                    'schema' => 'https',
                ],
                'visits_webhooks' => ['foo', 'bar'],
            ],
            'not_found_redirects' => [
                'regular_404' => 'aaabbbccc',
            ],
        ]];
    }
}
