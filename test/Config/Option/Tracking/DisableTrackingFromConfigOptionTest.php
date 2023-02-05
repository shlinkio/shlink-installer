<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Tracking;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Tracking\DisableTrackingFromConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DisableTrackingFromConfigOptionTest extends TestCase
{
    private DisableTrackingFromConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DisableTrackingFromConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DISABLE_TRACKING_FROM', $this->configOption->getEnvVar());
    }

    #[Test, DataProvider('provideAnswers')]
    public function expectedQuestionIsAsked(?string $answer): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'Provide a comma-separated list of IP addresses, CIDR blocks or wildcard addresses (1.2.*.*) from '
            . 'which you want tracking to be disabled',
        )->willReturn($answer);

        $result = $this->configOption->ask($io, []);

        self::assertEquals($answer, $result);
    }

    public static function provideAnswers(): iterable
    {
        yield 'no addresses' => [null];
        yield 'some addresses' => ['192.168.1.1,192.168.0.0/24'];
    }
}
