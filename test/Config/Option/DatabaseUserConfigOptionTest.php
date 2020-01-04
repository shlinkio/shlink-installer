<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Shlinkio\Shlink\Installer\Config\Option\DatabaseUserConfigOption;
use Shlinkio\Shlink\Installer\Util\PathCollection;
use Symfony\Component\Console\Style\StyleInterface;

class DatabaseUserConfigOptionTest extends TestCase
{
    /** @var DatabaseUserConfigOption */
    private $configOption;

    public function setUp(): void
    {
        $this->configOption = new DatabaseUserConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        $this->assertEquals(['entity_manager', 'connection', 'user'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask('Database username', null, Argument::any())->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        $this->assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }
}
