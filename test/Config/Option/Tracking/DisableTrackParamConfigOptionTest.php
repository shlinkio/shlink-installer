<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Tracking;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
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
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['tracking', 'disable_track_param'], $this->configOption->getDeprecatedPath());
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

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }

    /**
     * @test
     * @dataProvider provideConfigWithDeprecations
     */
    public function shouldBeAskedTransparentlyMigratesFromDeprecatedPath(PathCollection $config, bool $expected): void
    {
        $result = $this->configOption->shouldBeAsked($config);

        self::assertEquals($expected, $result);
        self::assertFalse($config->pathExists(['tracking', 'disable_track_param']));
        self::assertEquals(!$result, $config->pathExists(['DISABLE_TRACK_PARAM']));
    }

    public function provideConfigWithDeprecations(): iterable
    {
        yield 'deprecated is set, new is not' => [new PathCollection([
            'tracking' => [
                'disable_track_param' => 'something',
            ],
        ]), false];
        yield 'deprecated is not set, new is' => [new PathCollection([
            'DISABLE_TRACK_PARAM' => 'something',
        ]), false];
        yield 'neither deprecated nor new are set' => [new PathCollection(), true];
    }
}
