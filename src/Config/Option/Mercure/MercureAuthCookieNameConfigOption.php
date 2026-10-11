<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Mercure;

use Symfony\Component\Console\Style\StyleInterface;

class MercureAuthCookieNameConfigOption extends AbstractMercureEnabledConfigOption
{
    public function getEnvVar(): string
    {
        return 'MERCURE_AUTH_COOKIE_NAME';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        $version = $currentOptions[MercureVersionConfigOption::ENV_VAR] ?? 'legacy';
        return $io->ask(
            'Name of the auth cookie for the mercure hub',
            default: $version === 'legacy' ? 'mercureAuthorization' : '__Secure-mercure_access_token',
        );
    }

    public function getDependentOption(): string
    {
        return MercureVersionConfigOption::class;
    }
}
