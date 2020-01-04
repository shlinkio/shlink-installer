<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Plugin;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Shlinkio\Shlink\Installer\Config\Util\ExpectedConfigResolverInterface;
use Shlinkio\Shlink\Installer\Model\CustomizableAppConfig;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_diff;
use function array_keys;

/** @deprecated */
class RedirectsConfigCustomizer implements ConfigCustomizerInterface
{
    use ConfigOptionsValidatorsTrait;

    public const INVALID_SHORT_URL_REDIRECT_TO = 'INVALID_SHORT_URL_REDIRECT_TO';
    public const REGULAR_404_REDIRECT_TO = 'REGULAR_404_REDIRECT_TO';
    public const BASE_URL_REDIRECT_TO = 'BASE_URL_REDIRECT_TO';
    private const ALL_EXPECTED_KEYS = [
        self::INVALID_SHORT_URL_REDIRECT_TO,
        self::REGULAR_404_REDIRECT_TO,
        self::BASE_URL_REDIRECT_TO,
    ];

    /** @var array */
    private $expectedKeys;

    public function __construct(ExpectedConfigResolverInterface $resolver)
    {
        $this->expectedKeys = $resolver->resolveExpectedKeys(__CLASS__, self::ALL_EXPECTED_KEYS);
    }

    public function process(SymfonyStyle $io, CustomizableAppConfig $appConfig): void
    {
        $redirects = $appConfig->getRedirects();
        $hasRedirects = $appConfig->hasRedirects();
        $keysToAskFor = $hasRedirects ? array_diff($this->expectedKeys, array_keys($redirects)) : $this->expectedKeys;

        if (empty($keysToAskFor)) {
            return;
        }

        $io->title('REDIRECTS');
        foreach ($keysToAskFor as $key) {
            $redirects[$key] = $this->ask($io, $key);
        }
        $appConfig->setRedirects($redirects);
    }

    private function ask(SymfonyStyle $io, string $key)
    {
        switch ($key) {
            case self::INVALID_SHORT_URL_REDIRECT_TO:
                return $io->ask(
                    'Custom URL to redirect to when a user hits an invalid short URL (If no value is provided, the '
                    . 'user will see a default "404 not found" page)',
                    null,
                    [$this, 'validateUrl']
                );
            case self::REGULAR_404_REDIRECT_TO:
                return $io->ask(
                    'Custom URL to redirect to when a user hits a not found URL other than an invalid short URL '
                    . '(If no value is provided, the user will see a default "404 not found" page)',
                    null,
                    [$this, 'validateUrl']
                );
            case self::BASE_URL_REDIRECT_TO:
                return $io->ask(
                    'Custom URL to redirect to when a user hits Shlink\'s base URL (If no value is provided, the '
                    . 'user will see a default "404 not found" page)',
                    null,
                    [$this, 'validateUrl']
                );
        }

        return '';
    }
}
