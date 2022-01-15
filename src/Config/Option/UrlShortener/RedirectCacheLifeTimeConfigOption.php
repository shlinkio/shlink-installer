<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class RedirectCacheLifeTimeConfigOption extends BaseConfigOption implements DependentConfigOptionInterface
{
    use ConfigOptionsValidatorsTrait;

    public function getDeprecatedPath(): array
    {
        return ['url_shortener', 'redirect_cache_lifetime'];
    }

    public function getEnvVar(): string
    {
        return 'REDIRECT_CACHE_LIFETIME';
    }

    public function shouldBeAsked(PathCollection $currentOptions): bool
    {
        $redirectStatus = $currentOptions->getValueInPath(RedirectStatusCodeConfigOption::CONFIG_PATH);
        return $redirectStatus === 301 && parent::shouldBeAsked($currentOptions);
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): int
    {
        return $io->ask(
            'How long (in seconds) do you want your redirects to be cached by visitors?',
            '30',
            [$this, 'validatePositiveNumber'],
        );
    }

    public function getDependentOption(): string
    {
        return RedirectStatusCodeConfigOption::class;
    }
}
