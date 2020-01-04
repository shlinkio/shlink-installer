<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\Installer\Config\ConfigGenerator;
use Shlinkio\Shlink\Installer\Config\ConfigOptionsManagerInterface;
use Symfony\Component\Console\Style\StyleInterface;

class ConfigGeneratorTest extends TestCase
{
    /** @var ObjectProphecy */
    private $configOptionsManager;
    /** @var ObjectProphecy */
    private $io;

    public function setUp(): void
    {
        $this->configOptionsManager = $this->prophesize(ConfigOptionsManagerInterface::class);
        $this->io = $this->prophesize(StyleInterface::class);
    }

    /**
     * @test
     * @dataProvider
     */
    public function configuresExpectedPlugins(array $configOptionsGroups, ?array $enabledOptions): void
    {
        $generator = new ConfigGenerator($this->configOptionsManager->reveal(), $configOptionsGroups, $enabledOptions);
        $generator->generateConfigInteractively($this->io->reveal(), []);
    }
}
