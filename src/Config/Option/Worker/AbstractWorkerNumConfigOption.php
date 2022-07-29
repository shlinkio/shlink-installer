<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Worker;

use Shlinkio\Shlink\Installer\Config\Option\AbstractSwooleDependentConfigOption;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Symfony\Component\Console\Style\StyleInterface;

use function sprintf;

abstract class AbstractWorkerNumConfigOption extends AbstractSwooleDependentConfigOption
{
    use ConfigOptionsValidatorsTrait;

    public function ask(StyleInterface $io, array $currentOptions): int
    {
        $question = sprintf(
            '%s (Ignore this if you are not serving shlink with swoole or openswoole)',
            $this->getQuestionToAsk(),
        );
        return $io->ask(
            $question,
            '16',
            fn ($value) => $this->validateNumberGreaterThan($value, $this->getMinimumValue()),
        );
    }

    protected function getMinimumValue(): int
    {
        return 1;
    }

    abstract protected function getQuestionToAsk(): string;
}
