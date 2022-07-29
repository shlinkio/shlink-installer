<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Symfony\Component\Console\Style\StyleInterface;

class BasePathConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'BASE_PATH';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $io->ask(
            'What is the path from which shlink is going to be served? (It must include a leading bar, like "/shlink". '
            . 'Leave empty if you plan to serve shlink from the root of the domain)',
        ) ?? '';
    }
}
