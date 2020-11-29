<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\IpAnonymizationConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class IpAnonymizationConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private IpAnonymizationConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new IpAnonymizationConfigOption();
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['url_shortener', 'anonymize_remote_addr'], $this->configOption->getConfigPath());
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
            'Do you want visitors\' remote IP addresses to be anonymized before persisting them to the database?',
        )->willReturn($firstAnswer);
        $secondConfirm = $io->confirm('Do you still want to disable anonymization?', false)->willReturn($secondAnswer);
        $warning = $io->warning(
            'Careful! If you disable IP address anonymization, you will no longer be in compliance with the GDPR and '
            . 'other similar data protection regulations.',
        );

        $result = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedResult, $result);
        $firstConfirm->shouldHaveBeenCalledOnce();
        $secondConfirm->shouldHaveBeenCalledTimes($shouldWarn ? 1 : 0);
        $warning->shouldHaveBeenCalledTimes($shouldWarn ? 1 : 0);
    }

    public function provideConfirmAnswers(): iterable
    {
        yield 'anonymizing' => [true, true, false, true];
        yield 'anonymizing 2' => [true, false, false, true];
        yield 'anonymizing after warning' => [false, false, true, true];
        yield 'not anonymizing' => [false, true, true, false];
    }
}
