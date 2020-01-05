<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

use function explode;

class RedisServersConfigOption extends BaseConfigOption
{
    use ConfigOptionsValidatorsTrait;

    public function getConfigPath(): array
    {
        return ['cache', 'redis'];
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): ?array
    {
        $serves = $io->ask(
            'Provide a comma-separated list of redis server URIs which will be used for shared caching purposes under '
            . 'shlink multi-instance contexts (Leave empty if you don\'t want to use redis cache)',
        );

        return empty($serves) ? null : [
            'servers' => explode(',', $serves),
        ];
    }
}
