<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Plugin;

use Shlinkio\Shlink\Installer\Config\Util\ExpectedConfigResolverInterface;
use Shlinkio\Shlink\Installer\Exception\InvalidConfigOptionException;
use Shlinkio\Shlink\Installer\Model\CustomizableAppConfig;
use Shlinkio\Shlink\Installer\Util\StringGeneratorInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_diff;
use function array_keys;
use function is_numeric;
use function sprintf;

class ApplicationConfigCustomizer implements ConfigCustomizerInterface
{
    public const SECRET = 'SECRET';
    public const DISABLE_TRACK_PARAM = 'DISABLE_TRACK_PARAM';
    public const CHECK_VISITS_THRESHOLD = 'CHECK_VISITS_THRESHOLD';
    public const VISITS_THRESHOLD = 'VISITS_THRESHOLD';
    private const ALL_EXPECTED_KEYS = [
        self::SECRET,
        self::DISABLE_TRACK_PARAM,
        self::CHECK_VISITS_THRESHOLD,
        self::VISITS_THRESHOLD,
    ];

    /** @var array */
    private $expectedKeys;
    /** @var StringGeneratorInterface */
    private $stringGenerator;

    public function __construct(ExpectedConfigResolverInterface $resolver, StringGeneratorInterface $stringGenerator)
    {
        $this->expectedKeys = $resolver->resolveExpectedKeys(__CLASS__, self::ALL_EXPECTED_KEYS);
        $this->stringGenerator = $stringGenerator;
    }

    public function process(SymfonyStyle $io, CustomizableAppConfig $appConfig): void
    {
        $app = $appConfig->getApp();
        $keysToAskFor = $appConfig->hasApp() ? array_diff($this->expectedKeys, array_keys($app)) : $this->expectedKeys;

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
                    [$this, 'validateVisitsThreshold']
                );
        }

        return '';
    }

    public function validateVisitsThreshold($value): int
    {
        if (! is_numeric($value) || $value < 1) {
            throw new InvalidConfigOptionException(
                sprintf('Provided value "%s" is invalid. Expected a number greater than 1', $value)
            );
        }

        return (int) $value;
    }
}
