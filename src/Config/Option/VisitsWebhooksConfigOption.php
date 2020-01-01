<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Symfony\Component\Console\Style\SymfonyStyle;

class VisitsWebhooksConfigOption implements ConfigOptionInterface
{
    use ConfigOptionsValidatorsTrait;

    /** @var callable */
    private $swooleInstalled;

    public function __construct(callable $swooleInstalled)
    {
        $this->swooleInstalled = $swooleInstalled;
    }

    public function ask(SymfonyStyle $io, array $currentOptions)
    {
        return $io->ask(
            'Provide a comma-separated list of webhook URLs which will receive POST notifications when short URLs '
            . 'receive visits (Ignore this if you are not serving shlink with swoole)',
            null,
            [$this, 'splitAndValidateMultipleUrls']
        );
    }

    public function getConfigPath(): array
    {
        return ['url_shortener', 'visits_webhooks'];
    }

    public function shouldBeAsked(array $currentOptions): bool
    {
        return ($this->swooleInstalled)() && ! isset($currentOptions[self::class]);
    }
}
