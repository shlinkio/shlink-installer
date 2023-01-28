<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Visit;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Visit\VisitsWebhooksConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class VisitsWebhooksConfigOptionTest extends TestCase
{
    private VisitsWebhooksConfigOption $configOption;
    private bool $swooleInstalled;

    public function setUp(): void
    {
        $this->swooleInstalled = true;
        $this->configOption = new VisitsWebhooksConfigOption(fn () => $this->swooleInstalled);
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('VISITS_WEBHOOKS', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $urls = ['foo', 'bar'];
        $expectedAnswer = 'foo,bar';
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'Provide a comma-separated list of webhook URLs which will receive POST notifications when short URLs '
            . 'receive visits (Ignore this if you are not serving shlink with swoole or openswoole)',
            null,
            $this->anything(),
        )->willReturn($urls);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }

    /**
     * @test
     * @dataProvider provideCurrentOptions
     */
    public function shouldBeAskedWhenNotPresentAndSwooleIsInstalled(
        bool $swooleInstalled,
        array $currentOptions,
        bool $expected,
    ): void {
        $this->swooleInstalled = $swooleInstalled;
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public function provideCurrentOptions(): iterable
    {
        yield 'without swoole' => [false, [], false];
        yield 'with swoole and no config' => [true, [], true];
        yield 'with swoole and config' => [true, ['VISITS_WEBHOOKS' => []], false];
    }
}
