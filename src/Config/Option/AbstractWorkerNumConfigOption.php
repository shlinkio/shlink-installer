<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option;

use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Symfony\Component\Console\Style\SymfonyStyle;

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

    public function ask(SymfonyStyle $io, array $currentOptions)
    {
        $question = sprintf('%s (Ignore this if you are not serving shlink with swoole)', $this->getQuestionToAsk());
        return $io->ask($question, '16', [$this, 'validatePositiveNumber']);
    }

    public function shouldBeAsked(array $currentOptions): bool
    {
        return ($this->swooleInstalled)() && ! isset($currentOptions[self::class]);
    }

    abstract protected function getQuestionToAsk(): string;
}
