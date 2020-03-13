<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Closure;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Symfony\Component\Console\Style\StyleInterface;

use function sprintf;

abstract class AbstractWorkerNumConfigOption implements ConfigOptionInterface
{
    use ConfigOptionsValidatorsTrait;

    private Closure $swooleInstalled;

    public function __construct(callable $swooleInstalled)
    {
        $this->swooleInstalled = Closure::fromCallable($swooleInstalled);
    }

    public function ask(StyleInterface $io, PathCollection $currentOptions): int
    {
        $question = sprintf('%s (Ignore this if you are not serving shlink with swoole)', $this->getQuestionToAsk());
        return $io->ask($question, '16', [$this, 'validatePositiveNumber']);
    }

    public function shouldBeAsked(PathCollection $currentOptions): bool
    {
        return ($this->swooleInstalled)() && ! $currentOptions->pathExists($this->getConfigPath());
    }

    abstract protected function getQuestionToAsk(): string;
}
