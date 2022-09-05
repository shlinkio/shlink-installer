<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\UrlShortener;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Shlinkio\Shlink\Installer\Config\Option\UrlShortener\EnableTrailingSlashConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class EnableTrailingSlashConfigOptionTest extends TestCase
{
    use ProphecyTrait;

    private EnableTrailingSlashConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new EnableTrailingSlashConfigOption();
    }

    /** @test */
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('SHORT_URL_TRAILING_SLASH', $this->configOption->getEnvVar());
    }

    /** @test */
    public function expectedQuestionIsAsked(): void
    {
        $io = $this->prophesize(StyleInterface::class);
        $confirm = $io->confirm(
            'Do you want to support trailing slashes in short URLs? (https://doma.in/foo and https://doma.in/foo/ '
            . 'will be considered the same)',
            false,
        )->willReturn(true);

        $answer = $this->configOption->ask($io->reveal(), []);

        self::assertTrue($answer);
        $confirm->shouldHaveBeenCalledOnce();
    }
}
