<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\DependentConfigOptionInterface;
use Shlinkio\Shlink\Installer\Util\ArrayUtils;

/**
 * Base class for config options that depend on the redirect status code to be a permanent redirect (301 or 308)
 */
abstract class AbstractPermanentRedirectDependentConfigOption extends BaseConfigOption implements
    DependentConfigOptionInterface
{
    public function shouldBeAsked(array $currentOptions): bool
    {
        $redirectStatus = $currentOptions[RedirectStatusCodeConfigOption::ENV_VAR] ?? null;
        return $this->isPermanentRedirectStatus($redirectStatus) && parent::shouldBeAsked($currentOptions);
    }

    private function isPermanentRedirectStatus(int $redirectStatus): bool
    {
        return ArrayUtils::contains($redirectStatus, [301, 308]);
    }

    public function getDependentOption(): string
    {
        return RedirectStatusCodeConfigOption::class;
    }
}
