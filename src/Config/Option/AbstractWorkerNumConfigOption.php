<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

use function sprintf;

abstract class AbstractWorkerNumConfigOption implements ConfigOptionInterface
{
    use ConfigOptionsValidatorsTrait;

    /** @var callable */
    private $swooleInstalled;

    public function __construct(callable $swooleInstalled)
    {
        $this->swooleInstalled = $swooleInstalled;
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
