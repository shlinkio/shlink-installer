<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\UrlShortener;

use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class ObfuscateConfigOption extends BaseConfigOption
{
    public function getConfigPath(): array
    {
        return ['url_shortener', 'obfuscate_remote_addr'];
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): bool
    {
        $obfuscate = $io->confirm(
            'Do you want visitors\' remote IP addresses to be obfuscated before persisting them in the database?',
        );
        if ($obfuscate) {
            return true;
        }

        $io->warning(
            'Careful! If you disable IP address obfuscation, you will no longer be in compliance with the GDPR and '
            . 'other similar data protection regulations.',
        );
        return ! $io->confirm('Do you still want to disable obfuscation?', false);
    }
}
