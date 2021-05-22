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
        self::assertEquals(['tracking', 'disable_track_param'], $this->configOption->getConfigPath());
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
        self::assertFalse($config->pathExists(['app_options', 'disable_track_param']));
        self::assertEquals(!$result, $config->pathExists(['tracking', 'disable_track_param']));
    }

    public function provideConfigWithDeprecations(): iterable
    {
        yield 'deprecated is set, new is not' => [new PathCollection([
            'app_options' => [
                'disable_track_param' => 'something',
            ],
        ]), false];
        yield 'deprecated is not set, new is' => [new PathCollection([
            'tracking' => [
                'disable_track_param' => 'something',
            ],
        ]), false];
        yield 'neither deprecated nor new are set' => [new PathCollection(), true];
    }
}
