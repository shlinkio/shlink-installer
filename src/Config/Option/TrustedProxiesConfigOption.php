<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

class TrustedProxiesConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'TRUSTED_PROXIES';
    }

    public function ask(StyleInterface $io, array $currentOptions): string|int|null
    {
        if (! $io->confirm('Do you have more than one proxy in front of this Shlink instance?', default: false)) {
            return null;
        }

        $option = $io->choice(
            'How do you want your proxies IP addresses to be identified, so that the visitor IP address can be '
            . 'properly determined?',
            [
                'amount' => 'Just set the amount of proxies',
                'list' => 'Define a comma-separated list of IP addresses, CIDR blocks or wildcard addresses (1.2.*.*)',
            ],
            'list',
        );

        return match ($option) {
            'amount' => $io->ask(
                'How many proxies do you have in front of Shlink?',
                validator: ConfigOptionsValidator::validatePositiveNumber(...),
            ),
            default => $io->ask(
                'Provide a comma-separated list of your proxies\' IP addresses, CIDR blocks or wildcard '
                . 'addresses (1.2.*.*)',
                validator: static fn (string $value) => ConfigOptionsValidator::validateRequired(
                    $value,
                    'trusted proxies',
                ),
            ),
        };
    }
}
