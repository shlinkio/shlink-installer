<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\ObfuscateConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class ObfuscateConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private ObfuscateConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new ObfuscateConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        $this->assertEquals(['url_shortener', 'obfuscate_remote_addr'], $this->configOption->getConfigPath());
    }

    /**
     * @test
     * @dataProvider provideConfirmAnswers
     */
    public function expectedQuestionIsAsked(
        bool $firstAnswer,
        bool $secondAnswer,
        bool $shouldWarn,
        bool $expectedResult
    ): void {
        $io = $this->prophesize(StyleInterface::class);

        $firstConfirm = $io->confirm(
            'Do you want visitors\' remote IP addresses to be obfuscated before persisting them in the database?',
        )->willReturn($firstAnswer);
        $secondConfirm = $io->confirm('Do you still want to disable obfuscation?', false)->willReturn($secondAnswer);
        $warning = $io->warning(
            'Careful! If you disable IP address obfuscation, you will no longer be in compliance with the GDPR and '
            . 'other similar data protection regulations.',
        );

        $result = $this->configOption->ask($io->reveal(), new PathCollection());

        $this->assertEquals($expectedResult, $result);
        $firstConfirm->shouldHaveBeenCalledOnce();
        $secondConfirm->shouldHaveBeenCalledTimes($shouldWarn ? 1 : 0);
        $warning->shouldHaveBeenCalledTimes($shouldWarn ? 1 : 0);
    }

    public function provideConfirmAnswers(): iterable
    {
        yield 'obfuscating' => [true, true, false, true];
        yield 'obfuscating 2' => [true, false, false, true];
        yield 'obfuscating after warning' => [false, false, true, true];
        yield 'not obfuscating' => [false, true, true, false];
    }
}
