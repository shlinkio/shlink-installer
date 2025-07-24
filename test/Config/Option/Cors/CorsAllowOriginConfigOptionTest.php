<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Cors;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Cors\CorsAllowOriginConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class CorsAllowOriginConfigOptionTest extends TestCase
{
    private CorsAllowOriginConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new CorsAllowOriginConfigOption();
    }

    #[Test]
    public function expectedEnvVarIsReturned(): void
    {
        self::assertEquals('CORS_ALLOW_ORIGIN', $this->configOption->getEnvVar());
    }

    #[Test]
    #[TestWith(['*'])]
    #[TestWith(['<origin>'])]
    public function answerReturnedAsIsWhenNoAllowlistIsSelected(string $answer): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('choice')->with(
            'How do you want Shlink to determine which origins are allowed for CORS requests?',
            [
                '*' => 'All hosts are implicitly allowed (Access-Control-Allow-Origin is set to "*")',
                '<origin>' =>
                    'All hosts are explicitly allowed (Access-Control-Allow-Origin is set to the value in request\'s '
                    . 'Origin header)',
                'allowlist' => 'Provide a list of hosts that are allowed',
            ],
            '*',
        )->willReturn($answer);
        $io->expects($this->never())->method('ask');

        self::assertEquals($answer, $this->configOption->ask($io, []));
    }

    #[Test]
    public function allowListAskedWhenSelected(): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->method('choice')->willReturn('allowlist');
        $io->expects($this->once())->method('ask')->willReturn('foo,bar,baz');

        self::assertEquals('foo,bar,baz', $this->configOption->ask($io, []));
    }
}
