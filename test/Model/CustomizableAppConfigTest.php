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
            ->setUrlShortener($provided[Plugin\UrlShortenerConfigCustomizer::class]);

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
                Plugin\UrlShortenerConfigCustomizer::CHARS => '',
                Plugin\UrlShortenerConfigCustomizer::VALIDATE_URL => true,
                Plugin\UrlShortenerConfigCustomizer::ENABLE_NOT_FOUND_REDIRECTION => false,
                Plugin\UrlShortenerConfigCustomizer::NOT_FOUND_REDIRECT_TO => null,
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
                'shortcode_chars' => '',
                'validate_url' => true,
                'not_found_short_url' => [
                    'enable_redirection' => false,
                    'redirect_to' => null,
                ],
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
                Plugin\UrlShortenerConfigCustomizer::SCHEMA => 'http',
                Plugin\UrlShortenerConfigCustomizer::HOSTNAME => '',
                Plugin\UrlShortenerConfigCustomizer::CHARS => '',
                Plugin\UrlShortenerConfigCustomizer::VALIDATE_URL => true,
                Plugin\UrlShortenerConfigCustomizer::ENABLE_NOT_FOUND_REDIRECTION => false,
                Plugin\UrlShortenerConfigCustomizer::NOT_FOUND_REDIRECT_TO => null,
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
                'shortcode_chars' => '',
                'validate_url' => true,
                'not_found_short_url' => [
                    'enable_redirection' => false,
                    'redirect_to' => null,
                ],
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
                Plugin\UrlShortenerConfigCustomizer::CHARS => '123456789abcdef',
                Plugin\UrlShortenerConfigCustomizer::VALIDATE_URL => false,
                Plugin\UrlShortenerConfigCustomizer::ENABLE_NOT_FOUND_REDIRECTION => true,
                Plugin\UrlShortenerConfigCustomizer::NOT_FOUND_REDIRECT_TO => 'aaabbbccc',
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
                'shortcode_chars' => '123456789abcdef',
                'validate_url' => false,
                'not_found_short_url' => [
                    'enable_redirection' => true,
                    'redirect_to' => 'aaabbbccc',
                ],
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
                Plugin\UrlShortenerConfigCustomizer::CHARS => '123456789abcdef',
                Plugin\UrlShortenerConfigCustomizer::ENABLE_NOT_FOUND_REDIRECTION => true,
                Plugin\UrlShortenerConfigCustomizer::NOT_FOUND_REDIRECT_TO => 'aaabbbccc',
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
                'shortcode_chars' => '123456789abcdef',
                'not_found_short_url' => [
                    'enable_redirection' => true,
                    'redirect_to' => 'aaabbbccc',
                ],
            ],
        ]];
    }
}
