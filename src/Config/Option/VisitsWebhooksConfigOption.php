<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Closure;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

class VisitsWebhooksConfigOption implements ConfigOptionInterface
{
    use ConfigOptionsValidatorsTrait;

    private Closure $swooleInstalled;

    public function __construct(callable $swooleInstalled)
    {
        $this->swooleInstalled = Closure::fromCallable($swooleInstalled);
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): array
    {
        return $io->ask(
            'Provide a comma-separated list of webhook URLs which will receive POST notifications when short URLs '
            . 'receive visits (Ignore this if you are not serving shlink with swoole)',
            null,
            [$this, 'splitAndValidateMultipleUrls'],
        );
    }

    public function getConfigPath(): array
    {
        return ['url_shortener', 'visits_webhooks'];
    }

    public function shouldBeAsked(PathCollection $currentOptions): bool
    {
        return ($this->swooleInstalled)() && ! $currentOptions->pathExists($this->getConfigPath());
    }
}
