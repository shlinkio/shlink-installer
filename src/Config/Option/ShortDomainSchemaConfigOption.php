<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Symfony\Component\Console\Style\SymfonyStyle;

class ShortDomainSchemaConfigOption extends BaseConfigOption
{
    public function getConfigPath(): array
    {
        return ['url_shortener', 'domain', 'schema'];
    }

    public function ask(SymfonyStyle $io, array $currentOptions)
    {
        return $io->choice('Select schema for generated short URLs', ['http', 'https'], 'http');
    }
}
