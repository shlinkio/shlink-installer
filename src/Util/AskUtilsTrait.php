<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

/** @deprecated */
trait AskUtilsTrait
{
    /** @deprecated Use ConfigOptionsValidator::validateRequired instead */
    private function askRequired(StyleInterface $io, string $optionName, string|null $question = null): string
    {
        return $io->ask(
            $question ?? $optionName,
            validator: static fn ($value) => ConfigOptionsValidator::validateRequired($value, $optionName),
        );
    }
}
