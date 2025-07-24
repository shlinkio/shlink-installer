<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Server;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class LogsFormatConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'LOGS_FORMAT';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $io->choice(
            'In what format do you want Shlink to generate logs?',
            ['console', 'json'],
            'console',
        );
    }
}
