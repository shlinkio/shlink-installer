<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Matomo;

use Override;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Matomo\MatomoVisitDomainDimensionIdConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class MatomoVisitDomainDimensionIdConfigOptionTest extends TestCase
{
    private MatomoVisitDomainDimensionIdConfigOption $configOption;

    #[Override]
    public function setUp(): void
    {
        $this->configOption = new MatomoVisitDomainDimensionIdConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('MATOMO_VISIT_DOMAIN_DIMENSION_ID', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 12_345;
        $io = $this->createMock(StyleInterface::class);
        $io
            ->expects($this->once())
            ->method('ask')
            ->with('ID for the custom dimension where visit domain will be set, if any', null, $this->anything())
            ->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expectedAnswer, $answer);
    }
}
