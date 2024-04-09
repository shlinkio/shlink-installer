<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Server;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

class MemoryLimitConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'MEMORY_LIMIT';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $io->ask(
            'What is the maximum amount of RAM every process run by Shlink should be allowed to use? (Provide a '
            . 'number for bytes, a number followed by K for kilobytes, M for Megabytes or G for Gigabytes)',
            '512M',
            ConfigOptionsValidator::validateMemoryValue(...),
        );
    }
}
