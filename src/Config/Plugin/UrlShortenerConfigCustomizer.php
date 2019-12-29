<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Plugin;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Shlinkio\Shlink\Installer\Config\Util\ExpectedConfigResolverInterface;
use Shlinkio\Shlink\Installer\Model\CustomizableAppConfig;
use Shlinkio\Shlink\Installer\Util\AskUtilsTrait;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_diff;
use function array_keys;
use function extension_loaded;

class UrlShortenerConfigCustomizer implements ConfigCustomizerInterface
{
    use AskUtilsTrait;
    use ConfigOptionsValidatorsTrait;

    public const SCHEMA = 'SCHEMA';
    public const HOSTNAME = 'HOSTNAME';
    public const VALIDATE_URL = 'VALIDATE_URL';
    public const NOTIFY_VISITS_WEBHOOKS = 'CHECK_VISITS_WEBHOOKS';
    public const VISITS_WEBHOOKS = 'VISITS_WEBHOOKS';
    private const ALL_EXPECTED_KEYS = [
        self::SCHEMA,
        self::HOSTNAME,
        self::VALIDATE_URL,
        self::NOTIFY_VISITS_WEBHOOKS,
        self::VISITS_WEBHOOKS,
    ];
    private const SWOOLE_RELATED_KEYS = [
        self::NOTIFY_VISITS_WEBHOOKS,
        self::VISITS_WEBHOOKS,
    ];

    /** @var array */
    private $expectedKeys;
    /** @var callable */
    private $swooleEnabled;

    public function __construct(ExpectedConfigResolverInterface $resolver, ?callable $swooleEnabled = null)
    {
        $this->expectedKeys = $resolver->resolveExpectedKeys(__CLASS__, self::ALL_EXPECTED_KEYS);
        $this->swooleEnabled = $swooleEnabled ?? static function (): bool {
            return extension_loaded('swoole');
        };
    }

    public function process(SymfonyStyle $io, CustomizableAppConfig $appConfig): void
    {
        $urlShortener = $appConfig->getUrlShortener();
        $doImport = $appConfig->hasUrlShortener();
        $keysToAskFor = $doImport ? array_diff($this->expectedKeys, array_keys($urlShortener)) : $this->expectedKeys;
        if (! ($this->swooleEnabled)()) {
            $keysToAskFor = array_diff($keysToAskFor, self::SWOOLE_RELATED_KEYS);
        }

        if (empty($keysToAskFor)) {
            return;
        }

        $io->title('URL SHORTENER');
        foreach ($keysToAskFor as $key) {
            // Skip visits webhooks when the user decided not to notify webhooks on visits
            if ($key === self::VISITS_WEBHOOKS && ! $urlShortener[self::NOTIFY_VISITS_WEBHOOKS]) {
                continue;
            }

            $urlShortener[$key] = $this->ask($io, $key);
        }
        $appConfig->setUrlShortener($urlShortener);
    }

    private function ask(SymfonyStyle $io, string $key)
    {
        switch ($key) {
            case self::SCHEMA:
                return $io->choice(
                    'Select schema for generated short URLs',
                    ['http', 'https'],
                    'http'
                );
            case self::HOSTNAME:
                return $this->askRequired($io, 'domain', 'Default domain for generated short URLs');
            case self::VALIDATE_URL:
                return $io->confirm('Do you want to validate long urls by 200 HTTP status code on response');
            case self::NOTIFY_VISITS_WEBHOOKS:
                return $io->confirm(
                    'Do you want to configure external webhooks to receive POST notifications every time a short URL '
                    . 'receives a visit? (Ignore this if you are not serving shlink with swoole)',
                    false
                );
            case self::VISITS_WEBHOOKS:
                return $io->ask(
                    'Provide a comma-separated list of webhook URLs which will receive the POST notifications',
                    null,
                    [$this, 'splitAndValidateMultipleUrls']
                );
        }

        return '';
    }
}
