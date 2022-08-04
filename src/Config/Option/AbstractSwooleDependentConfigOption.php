<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Closure;

abstract class AbstractSwooleDependentConfigOption extends BaseConfigOption
{
    private Closure $swooleInstalled;

    public function __construct(callable $swooleInstalled)
    {
        $this->swooleInstalled = Closure::fromCallable($swooleInstalled);
    }

    public function shouldBeAsked(array $currentOptions): bool
    {
        return ($this->swooleInstalled)() && parent::shouldBeAsked($currentOptions);
    }
}
