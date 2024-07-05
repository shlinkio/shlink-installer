<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Robots;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Robots\RobotsAllowAllShortUrlsConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RobotsAllowAllShortUrlsConfigOptionTest extends TestCase
{
    private RobotsAllowAllShortUrlsConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RobotsAllowAllShortUrlsConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('ROBOTS_ALLOW_ALL_SHORT_URLS', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('confirm')->with(
            'Do you want all short URLs to be crawlable/allowed by the robots.txt file? '
            . 'You can still allow them individually, regardless of this.',
            false,
        )->willReturn(true);

        $answer = $this->configOption->ask($io, []);

        self::assertTrue($answer);
    }
}
