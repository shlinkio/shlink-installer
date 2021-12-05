<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Visit;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\Visit\VisitsWebhooksConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class VisitsWebhooksConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private VisitsWebhooksConfigOption $configOption;
    private bool $swooleInstalled;

    public function setUp(): void
    {
        $this->swooleInstalled = true;
        $this->configOption = new VisitsWebhooksConfigOption(fn () => $this->swooleInstalled);
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['url_shortener', 'visits_webhooks'], $this->configOption->getConfigPath());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = [];
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'Provide a comma-separated list of webhook URLs which will receive POST notifications when short URLs '
            . 'receive visits (Ignore this if you are not serving shlink with swoole or openswoole)',
            null,
            Argument::any(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }

    /**
     * @test
     * @dataProvider provideCurrentOptions
     */
    public function shouldBeAskedWhenNotPresentAndSwooleIsInstalled(
        bool $swooleInstalled,
        PathCollection $currentOptions,
        bool $expected,
    ): void {
        $this->swooleInstalled = $swooleInstalled;
        self::assertEquals($expected, $this->configOption->shouldBeAsked($currentOptions));
    }

    public function provideCurrentOptions(): iterable
    {
        yield 'without swoole' => [false, new PathCollection(), false];
        yield 'with swoole and no config' => [true, new PathCollection(), true];
        yield 'with swoole and config' => [true, new PathCollection([
            'url_shortener' => [
                'visits_webhooks' => [],
            ],
        ]), false];
    }
}
