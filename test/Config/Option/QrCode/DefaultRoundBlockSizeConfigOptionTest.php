<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\QrCode;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\QrCode\DefaultRoundBlockSizeConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class DefaultRoundBlockSizeConfigOptionTest extends TestCase
{
    private DefaultRoundBlockSizeConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new DefaultRoundBlockSizeConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('DEFAULT_QR_CODE_ROUND_BLOCK_SIZE', $this->configOption->getEnvVar());
    }

    /**
     * @test
     * @dataProvider provideAnswers
     */
    public function expectedQuestionIsAsked(string $providedAnswer, bool $expected): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('choice')->with(
            'Do you want the QR codes block size to be rounded by default? QR codes could end up having some extra '
            . 'margin, but it will improve readability',
            [
                'yes' => 'Round block size, improving readability',
                'no' => 'Do not round block size, preventing extra margin',
            ],
            'yes',
        )->willReturn($providedAnswer);

        $answer = $this->configOption->ask($io, []);

        self::assertEquals($expected, $answer);
    }

    public static function provideAnswers(): iterable
    {
        yield 'yes' => ['yes', true];
        yield 'no' => ['no', false];
    }
}
