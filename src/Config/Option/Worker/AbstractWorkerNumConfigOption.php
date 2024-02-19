<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Worker;

use Shlinkio\Shlink\Installer\Config\Option\Server\AbstractAsyncRuntimeDependentConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidator;
use Symfony\Component\Console\Style\StyleInterface;

abstract class AbstractWorkerNumConfigOption extends AbstractAsyncRuntimeDependentConfigOption
{
    public function ask(StyleInterface $io, array $currentOptions): int
    {
        return $io->ask(
            $this->getQuestionToAsk(),
            '16',
            fn ($value) => ConfigOptionsValidator::validateNumberGreaterThan($value, $this->getMinimumValue()),
        );
    }

    protected function getMinimumValue(): int
    {
        return 1;
    }

    abstract protected function getQuestionToAsk(): string;
}
