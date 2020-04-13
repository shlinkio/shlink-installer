<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Mercure;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\Mercure\EnableMercureConfigOption;
use Shlinkio\Shlink\Installer\Config\Option\Mercure\MercureInternalUrlConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class MercureInternalUrlConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private MercureInternalUrlConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new MercureInternalUrlConfigOption(fn() => true);
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        $this->assertEquals(['mercure', 'internal_hub_url'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'foobar.com';
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'Internal URL of the mercure hub server (leave empty to use the public one)',
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        $this->assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function dependsOnMercureEnabled(): void
    {
        $this->assertEquals(EnableMercureConfigOption::class, $this->configOption->getDependentOption());
    }

    /**
     * @test
     * @dataProvider provideCurrentOptions
     */
    public function shouldBeAskedOnlyIfMercureIsEnabled(PathCollection $currentOptions, bool $expected): void
    {
        $this->assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public function provideCurrentOptions(): iterable
    {
        $enabledPathCollection = new PathCollection();
        $enabledPathCollection->setValueInPath(true, EnableMercureConfigOption::CONFIG_PATH);

        yield 'mercure enabled' => [$enabledPathCollection, true];
        yield 'mercure not enabled' => [new PathCollection(), false];
    }
}
