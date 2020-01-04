<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\SymfonyStyle;

class ValidateUrlConfigOption extends BaseConfigOption
{
    public function getConfigPath(): array
    {
        return ['url_shortener', 'validate_url'];
    }

    public function ask(SymfonyStyle $io, PathCollection $currentOptions): bool
    {
        return $io->confirm('Do you want to validate long urls by 200 HTTP status code on response?');
    }
}
