<?php
declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Plugin;

use Shlinkio\Shlink\Installer\Config\Util\ExpectedConfigResolverInterface;
use Shlinkio\Shlink\Installer\Model\CustomizableAppConfig;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_diff;
use function array_keys;

class LanguageConfigCustomizer implements ConfigCustomizerInterface
{
    public const DEFAULT_LANG = 'DEFAULT';
    private const ALL_EXPECTED_KEYS = [
        self::DEFAULT_LANG,
    ];

    private const SUPPORTED_LANGUAGES = ['en', 'es'];

    /** @var array */
    private $expectedKeys;

    public function __construct(ExpectedConfigResolverInterface $resolver)
    {
        $this->expectedKeys = $resolver->resolveExpectedKeys(__CLASS__, self::ALL_EXPECTED_KEYS);
    }

    public function process(SymfonyStyle $io, CustomizableAppConfig $appConfig): void
    {
        $lang = $appConfig->getLanguage();
        $keysToAskFor = $appConfig->hasLanguage()
            ? array_diff($this->expectedKeys, array_keys($lang))
            : $this->expectedKeys;

        if (empty($keysToAskFor)) {
            return;
        }

        $io->title('LANGUAGE');
        foreach ($keysToAskFor as $key) {
            $lang[$key] = $this->ask($io, $key);
        }
        $appConfig->setLanguage($lang);
    }

    private function ask(SymfonyStyle $io, string $key)
    {
        switch ($key) {
            case self::DEFAULT_LANG:
                return $this->chooseLanguage($io, 'Select default language for the application error pages');
        }

        return '';
    }

    private function chooseLanguage(SymfonyStyle $io, string $message): string
    {
        return $io->choice($message, self::SUPPORTED_LANGUAGES, self::SUPPORTED_LANGUAGES[0]);
    }
}
