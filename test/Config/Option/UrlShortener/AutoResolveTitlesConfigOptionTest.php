<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\AutoResolveTitlesConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class AutoResolveTitlesConfigOptionTest extends TestCase
{
    private AutoResolveTitlesConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new AutoResolveTitlesConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('AUTO_RESOLVE_TITLES', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('confirm')->with(
            'Do you want Shlink to resolve the short URL title based on the long URL\'s title tag (if any)? '
                . 'Otherwise, it will be kept empty unless explicitly provided.',
        )->willReturn(true);

        $answer = $this->configOption->ask($io, []);

        self::assertTrue($answer);
    }
}
