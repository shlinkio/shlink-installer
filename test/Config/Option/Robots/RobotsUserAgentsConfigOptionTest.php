<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Robots;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Robots\RobotsUserAgentsConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RobotsUserAgentsConfigOptionTest extends TestCase
{
    private RobotsUserAgentsConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new RobotsUserAgentsConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('ROBOTS_USER_AGENTS', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'Provide a comma-separated list of user agents for your robots.txt file. Defaults to all user agents (*)',
        )->willReturn('foo,bar');

        $answer = $this->configOption->ask($io, []);

        self::assertEquals('foo,bar', $answer);
    }
}
