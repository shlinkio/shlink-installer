<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Closure;
use Shlinkio\Shlink\Config\Collection\PathCollection;

abstract class AbstractSwooleDependentConfigOption implements ConfigOptionInterface
{
    private Closure $swooleInstalled;

    public function __construct(callable $swooleInstalled)
    {
        $this->swooleInstalled = Closure::fromCallable($swooleInstalled);
    }

    public function shouldBeAsked(PathCollection $currentOptions): bool
    {
        return ($this->swooleInstalled)() && ! $currentOptions->pathExists($this->getConfigPath());
    }
}
