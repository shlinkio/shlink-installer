<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Mercure;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Config\Collection\PathCollection;
use Shlinkio\Shlink\Installer\Config\Option\Mercure\MercurePublicUrlConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class MercurePublicUrlConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private MercurePublicUrlConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new MercurePublicUrlConfigOption(fn () => false);
    }

    /** @test */
    public function returnsExpectedConfig(): void
    {
        self::assertEquals(['mercure', 'public_hub_url'], $this->configOption->getDeprecatedPath());
        self::assertEquals('MERCURE_PUBLIC_HUB_URL', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $expectedAnswer = 'foobar.com';
        $io = $this->prophesize(StyleInterface::class);
        $ask = $io->ask('Public URL of the mercure hub server', Argument::cetera())->willReturn($expectedAnswer);

        $answer = $this->configOption->ask($io->reveal(), new PathCollection());

        self::assertEquals($expectedAnswer, $answer);
        $ask->shouldHaveBeenCalledOnce();
    }
}
