<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option\Cache;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\Cache\CacheNamespaceConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class CacheNamespaceConfigOptionTest extends TestCase
{
    private CacheNamespaceConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new CacheNamespaceConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('CACHE_NAMESPACE', $this->configOption->getEnvVar());
    }

    #[Test]
    public function expectedQuestionIsAsked(): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('ask')->with(
            'Prefix for cache entry keys. (Change this if you run multiple Shlink instances on this server, or they '
            . 'share the same redis instance)',
            'Shlink',
        )->willReturn('Shlink');

        $answer = $this->configOption->ask($io, []);

        self::assertEquals('Shlink', $answer);
    }
}
