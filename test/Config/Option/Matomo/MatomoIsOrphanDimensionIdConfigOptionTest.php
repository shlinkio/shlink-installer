<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Matomo;

use Override;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Matomo\MatomoIsOrphanDimensionIdConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class MatomoIsOrphanDimensionIdConfigOptionTest extends TestCase
{
    private MatomoIsOrphanDimensionIdConfigOption $configOption;

    #[Override]
    public function setUp(): void
    {
        $this->configOption = new MatomoIsOrphanDimensionIdConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('MATOMO_IS_ORPHAN_DIMENSION_ID', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 12_345;
        $io = $this->createMock(StyleInterface::class);
        $io
            ->expects($this->once())
            ->method('ask')
            ->with('ID for the custom dimension where is_orphan flag will be set, if any', null, $this->anything())
            ->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
