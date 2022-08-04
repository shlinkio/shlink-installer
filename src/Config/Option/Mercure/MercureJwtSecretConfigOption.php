<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Mercure;

use Shlinkio\Shlink\Installer\Util\AskUtilsTrait;
use Symfony\Component\Console\Style\StyleInterface;

class MercureJwtSecretConfigOption extends AbstractMercureEnabledConfigOption
{
    use AskUtilsTrait;

    public function getEnvVar(): string
    {
        return 'MERCURE_JWT_SECRET';
    }

    public function ask(StyleInterface $io, array $currentOptions): string
    {
        return $this->askRequired($io, 'JWT secret', 'The secret key known by the mercure hub server to validate JWTs');
    }
}
