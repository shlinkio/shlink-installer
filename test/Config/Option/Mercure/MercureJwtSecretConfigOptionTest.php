<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Mercure;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\Mercure\MercureJwtSecretConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class MercureJwtSecretConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private MercureJwtSecretConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new MercureJwtSecretConfigOption(fn () => false);
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('MERCURE_JWT_SECRET', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'foobar.com';
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask(
            'The secret key known by the mercure hub server to validate JWTs',
            Argument::cetera(),
        )->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), []);

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }
}
