<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Matomo;

use Override;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Matomo\MatomoVisitTypeDimensionIdConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class MatomoVisitTypeDimensionIdConfigOptionTest extends TestCase
{
    private MatomoVisitTypeDimensionIdConfigOption $configOption;

    #[Override]
    public function setUp(): void
    {
        $this->configOption = new MatomoVisitTypeDimensionIdConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('MATOMO_VISIT_TYPE_DIMENSION_ID', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 12_345;
        $io = $this->createMock(StyleInterface::class);
        $io
            ->expects($this->once())
            ->method('ask')
            ->with('ID for the custom dimension where visit type will be set', null, $this->anything())
            ->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
