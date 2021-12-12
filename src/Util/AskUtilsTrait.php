<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

use Shlinkio\Shlink\Installer\Exception\MissingRequiredOptionException;
use Symfony\Component\Console\Style\StyleInterface;

trait AskUtilsTrait
{
    private function askRequired(StyleInterface $io, string $optionNameOrQuestion, ?string $question = null): string
    {
        return $io->ask($question ?? $optionNameOrQuestion, null, static function ($value) use ($optionNameOrQuestion) {
            if (empty($value)) {
                throw MissingRequiredOptionException::fromOption($optionNameOrQuestion);
            }

            return $value;
        });
    }
}
