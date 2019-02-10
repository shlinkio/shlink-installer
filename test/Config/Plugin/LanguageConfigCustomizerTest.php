<?php
declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Plugin;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\Installer\Config\Plugin\LanguageConfigCustomizer;
use Shlinkio\Shlink\Installer\Model\CustomizableAppConfig;
use ShlinkioTest\Shlink\Installer\Util\TestUtilsTrait;
use Symfony\Component\Console\Style\SymfonyStyle;

class LanguageConfigCustomizerTest extends TestCase
{
    use TestUtilsTrait;

    /** @var LanguageConfigCustomizer */
    private $plugin;
    /** @var ObjectProphecy */
    private $io;

    public function setUp(): void
    {
        $this->io = $this->prophesize(SymfonyStyle::class);
        $this->io->title(Argument::any())->willReturn(null);
        $this->plugin = new LanguageConfigCustomizer($this->createExpectedConfigResolverMock());
    }

    /**
     * @test
     */
    public function configIsRequestedToTheUser(): void
    {
        $choice = $this->io->choice(Argument::cetera())->willReturn('en');
        $config = new CustomizableAppConfig();

        $this->plugin->process($this->io->reveal(), $config);

        $this->assertTrue($config->hasLanguage());
        $this->assertEquals([
            'DEFAULT' => 'en',
        ], $config->getLanguage());
        $choice->shouldHaveBeenCalledOnce();
    }

    /**
     * @test
     */
    public function onlyMissingOptionsAreAsked(): void
    {
        $choice = $this->io->choice(Argument::cetera())->willReturn('es');
        $config = new CustomizableAppConfig();

        $this->plugin->process($this->io->reveal(), $config);

        $this->assertEquals([
            'DEFAULT' => 'es',
        ], $config->getLanguage());
        $choice->shouldHaveBeenCalledOnce();
    }

    /**
     * @test
     */
    public function noQuestionsAskedIfImportedConfigContainsEverything(): void
    {
        $choice = $this->io->choice(Argument::cetera())->willReturn('en');

        $config = new CustomizableAppConfig();
        $config->setLanguage([
            'DEFAULT' => 'es',
        ]);

        $this->plugin->process($this->io->reveal(), $config);

        $this->assertEquals([
            'DEFAULT' => 'es',
        ], $config->getLanguage());
        $choice->shouldNotHaveBeenCalled();
    }
}
