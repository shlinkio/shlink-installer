<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Robots;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RobotsUserAgentsConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'ROBOTS_USER_AGENTS';
    }

    public function ask(StyleInterface $io, array $currentOptions): ?string
    {
        return $io->ask(
            'Provide a comma-separated list of user agents for your robots.txt file. Defaults to all user agents (*)',
        );
    }
}
