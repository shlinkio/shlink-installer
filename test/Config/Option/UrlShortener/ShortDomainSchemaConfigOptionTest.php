<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\ShortDomainSchemaConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class ShortDomainSchemaConfigOptionTest extends TestCase
{
    private ShortDomainSchemaConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new ShortDomainSchemaConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('IS_HTTPS_ENABLED', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = true;
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('confirm')->with('Is HTTPS enabled on this server?')->willReturn(
            $expectedAnswer,
        );

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
