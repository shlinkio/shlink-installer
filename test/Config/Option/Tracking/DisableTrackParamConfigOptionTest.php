<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Tracking;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\Tracking\DisableTrackParamConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DisableTrackParamConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private DisableTrackParamConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DisableTrackParamConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DISABLE_TRACK_PARAM', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'the_answer';
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'Provide a parameter name that you will be able to use to disable tracking on specific request to '
            . 'short URLs (leave empty and this feature won\'t be enabled)',
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), []);

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }

    /**
     * @test
     * @dataProvider provideConfig
     */
    public function shouldBeAskedReturnsExpectedValue(array $config, bool $expected): void
    {
        $result = $this->configOption->shouldBeAsked($config);
        self::assertEquals($expected, $result);
    }

    public function provideConfig(): iterable
    {
        yield 'config is set' => [['DISABLE_TRACK_PARAM' => 'something'], false];
        yield 'config is not set' => [[], true];
    }
}
