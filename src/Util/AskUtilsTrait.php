<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

use Shlinkio\Shlink\Installer\Exception\MissingRequiredOptionException;
use Symfony\Component\Console\Style\SymfonyStyle;

trait AskUtilsTrait
{
    private function askRequired(SymfonyStyle $io, string $optionName, string $question): string
    {
        return $io->ask($question, null, static function ($value) use ($optionName) {
            if (empty($value)) {
                throw MissingRequiredOptionException::fromOption($optionName);
            }

            return $value;
        });
    }
}
