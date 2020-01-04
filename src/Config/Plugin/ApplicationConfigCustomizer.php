<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Plugin;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Shlinkio\Shlink\Installer\Config\Util\ExpectedConfigResolverInterface;
use Shlinkio\Shlink\Installer\Model\CustomizableAppConfig;
use Shlinkio\Shlink\Installer\Util\StringGeneratorInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_diff;
use function array_keys;
use function extension_loaded;

/** @deprecated */
class ApplicationConfigCustomizer implements ConfigCustomizerInterface
{
    use ConfigOptionsValidatorsTrait;

    public const SECRET = 'SECRET';
    public const DISABLE_TRACK_PARAM = 'DISABLE_TRACK_PARAM';
    public const CHECK_VISITS_THRESHOLD = 'CHECK_VISITS_THRESHOLD';
    public const VISITS_THRESHOLD = 'VISITS_THRESHOLD';
    public const BASE_PATH = 'BASE_PATH';
    public const WEB_WORKER_NUM = 'WEB_WORKER_NUM';
    public const TASK_WORKER_NUM = 'TASK_WORKER_NUM';
    private const ALL_EXPECTED_KEYS = [
        self::SECRET,
        self::DISABLE_TRACK_PARAM,
        self::CHECK_VISITS_THRESHOLD,
        self::VISITS_THRESHOLD,
        self::BASE_PATH,
        self::WEB_WORKER_NUM,
        self::TASK_WORKER_NUM,
    ];
    private const SWOOLE_RELATED_KEYS = [
        self::WEB_WORKER_NUM,
        self::TASK_WORKER_NUM,
    ];

    /** @var array */
    private $expectedKeys;
    /** @var StringGeneratorInterface */
    private $stringGenerator;
    /** @var callable */
    private $swooleEnabled;

    public function __construct(
        ExpectedConfigResolverInterface $resolver,
        StringGeneratorInterface $stringGenerator,
        ?callable $swooleEnabled = null
    ) {
        $this->expectedKeys = $resolver->resolveExpectedKeys(__CLASS__, self::ALL_EXPECTED_KEYS);
        $this->stringGenerator = $stringGenerator;
        $this->swooleEnabled = $swooleEnabled ?? static function (): bool {
            return extension_loaded('swoole');
        };
    }

    public function process(SymfonyStyle $io, CustomizableAppConfig $appConfig): void
    {
        $app = $appConfig->getApp();
        $keysToAskFor = $appConfig->hasApp() ? array_diff($this->expectedKeys, array_keys($app)) : $this->expectedKeys;
        if (! ($this->swooleEnabled)()) {
            $keysToAskFor = array_diff($keysToAskFor, self::SWOOLE_RELATED_KEYS);
        }

        if (empty($keysToAskFor)) {
            return;
        }

        $io->title('APPLICATION');
        foreach ($keysToAskFor as $key) {
            // Skip visits threshold when the user decided not to check visits on deletions
            if ($key === self::VISITS_THRESHOLD && ! $app[self::CHECK_VISITS_THRESHOLD]) {
                continue;
            }

            $app[$key] = $this->ask($io, $key);
        }
        $appConfig->setApp($app);
    }

    private function ask(SymfonyStyle $io, string $key)
    {
        switch ($key) {
            case self::SECRET:
                // This won't actually ask anything, just generate the chars. Asking for this was confusing for users
                return $this->stringGenerator->generateRandomString(32);
            case self::DISABLE_TRACK_PARAM:
                return $io->ask(
                    'Provide a parameter name that you will be able to use to disable tracking on specific request to '
                    . 'short URLs (leave empty and this feature won\'t be enabled)'
                );
            case self::CHECK_VISITS_THRESHOLD:
                return $io->confirm(
                    'Do you want to enable a safety check which will not allow short URLs to be deleted when they '
                    . 'have more than a specific amount of visits?'
                );
            case self::VISITS_THRESHOLD:
                return $io->ask(
                    'What is the amount of visits from which the system will not allow short URLs to be deleted?',
                    '15',
                    [$this, 'validatePositiveNumber']
                );
            case self::BASE_PATH:
                return $io->ask(
                    'What is the path from which shlink is going to be served? (Leave empty if you plan to serve '
                    . 'shlink from the root of the domain)'
                ) ?? '';
            case self::WEB_WORKER_NUM:
                return $io->ask(
                    'How many concurrent requests do you want Shlink to be able to serve? '
                    . '(Ignore this if you are not serving shlink with swoole)',
                    '16',
                    [$this, 'validatePositiveNumber']
                );
            case self::TASK_WORKER_NUM:
                return $io->ask(
                    'How many concurrent background tasks do you want Shlink to be able to execute? '
                    . '(Ignore this if you are not serving shlink with swoole)',
                    '16',
                    [$this, 'validatePositiveNumber']
                );
        }

        return '';
    }
}
