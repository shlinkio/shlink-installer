<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Cors;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class CorsAllowOriginConfigOption extends BaseConfigOption
{
    public function getEnvVar(): string
    {
        return 'CORS_ALLOW_ORIGIN';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        $answer = $io->choice(
            'How do you want Shlink to determine which origins are allowed for CORS requests?',
            [
                '*' => 'All hosts are implicitly allowed (Access-Control-Allow-Origin is set to "*")',
                '<origin>' =>
                    'All hosts are explicitly allowed (Access-Control-Allow-Origin is set to the value in request\'s '
                    . 'Origin header)',
                'allowlist' => 'Provide a list of hosts that are allowed',
            ],
            '*',
        );

        return $answer !== 'allowlist' ? $answer : $io->ask(
            'Provide a comma-separated list of origins that should be allowed to perform CORS requests to Shlink',
        );
    }
}
