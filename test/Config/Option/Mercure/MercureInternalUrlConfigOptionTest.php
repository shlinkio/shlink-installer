<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Mercure;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
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
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('MERCURE_INTERNAL_HUB_URL', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'foobar.com';
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'Internal URL of the mercure hub server (leave empty to use the public one)',
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), []);

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function dependsOnMercureEnabled(): void
    {
        self::assertEquals(EnableMercureConfigOption::class, $this->configOption->getDependentOption());
    }

    /**
     * @test
     * @dataProvider provideCurrentOptions
     */
    public function shouldBeAskedOnlyIfMercureIsEnabled(array $currentOptions, bool $expected): void
    {
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public function provideCurrentOptions(): iterable
    {
        yield 'mercure enabled' => [[EnableMercureConfigOption::ENV_VAR => true], true];
        yield 'mercure not enabled' => [[], false];
    }
}
