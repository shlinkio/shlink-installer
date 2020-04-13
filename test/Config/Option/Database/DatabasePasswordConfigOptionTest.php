<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Database;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\Database\DatabasePasswordConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DatabasePasswordConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private DatabasePasswordConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DatabasePasswordConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        $this->assertEquals(['entity_manager', 'connection', 'password'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask('Database password', null, Argument::cetera())->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        $this->assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }
}
