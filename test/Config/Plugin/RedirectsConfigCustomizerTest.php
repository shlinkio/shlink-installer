<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Plugin;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\Installer\Config\Plugin\RedirectsConfigCustomizer;
use Shlinkio\Shlink\Installer\Model\CustomizableAppConfig;
use ShlinkioTest\Shlink\Installer\Util\TestUtilsTrait;
use Symfony\Component\Console\Style\SymfonyStyle;

class RedirectsConfigCustomizerTest extends TestCase
{
    use TestUtilsTrait;

    /** @var RedirectsConfigCustomizer */
    private $plugin;
    /** @var ObjectProphecy */
    private $io;

    public function setUp(): void
    {
        $this->io = $this->prophesize(SymfonyStyle::class);
        $this->io->title(Argument::any())->willReturn(null);

        $this->plugin = new RedirectsConfigCustomizer($this->createExpectedConfigResolverMock());
    }

    /** @test */
    public function configIsRequestedToTheUser(): void
    {
        $ask = $this->io->ask(Argument::cetera())->willReturn('https://www.google.com');
        $config = new CustomizableAppConfig();

        $this->plugin->process($this->io->reveal(), $config);

        $this->assertTrue($config->hasRedirects());
        $this->assertEquals([
            RedirectsConfigCustomizer::INVALID_SHORT_URL_REDIRECT_TO => 'https://www.google.com',
            RedirectsConfigCustomizer::REGULAR_404_REDIRECT_TO => 'https://www.google.com',
            RedirectsConfigCustomizer::BASE_URL_REDIRECT_TO => 'https://www.google.com',
        ], $config->getRedirects());
        $ask->shouldHaveBeenCalledTimes(3);
    }

    /** @test */
    public function onlyMissingOptionsAreAsked(): void
    {
        $ask = $this->io->ask(Argument::cetera())->willReturn('https://www.google.com');
        $config = new CustomizableAppConfig();
        $config->setRedirects([
            RedirectsConfigCustomizer::INVALID_SHORT_URL_REDIRECT_TO => 'foo',
            RedirectsConfigCustomizer::BASE_URL_REDIRECT_TO => 'bar',
        ]);

        $this->plugin->process($this->io->reveal(), $config);

        $this->assertEquals([
            RedirectsConfigCustomizer::INVALID_SHORT_URL_REDIRECT_TO => 'foo',
            RedirectsConfigCustomizer::REGULAR_404_REDIRECT_TO => 'https://www.google.com',
            RedirectsConfigCustomizer::BASE_URL_REDIRECT_TO => 'bar',
        ], $config->getRedirects());
        $ask->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function noQuestionsAskedIfImportedConfigContainsEverything(): void
    {
        $ask = $this->io->ask(Argument::cetera())->willReturn('https://www.google.com');

        $config = new CustomizableAppConfig();
        $config->setRedirects([
            RedirectsConfigCustomizer::INVALID_SHORT_URL_REDIRECT_TO => 'foo',
            RedirectsConfigCustomizer::REGULAR_404_REDIRECT_TO => 'bar',
            RedirectsConfigCustomizer::BASE_URL_REDIRECT_TO => 'baz',
        ]);

        $this->plugin->process($this->io->reveal(), $config);

        $this->assertEquals([
            RedirectsConfigCustomizer::INVALID_SHORT_URL_REDIRECT_TO => 'foo',
            RedirectsConfigCustomizer::REGULAR_404_REDIRECT_TO => 'bar',
            RedirectsConfigCustomizer::BASE_URL_REDIRECT_TO => 'baz',
        ], $config->getRedirects());
        $ask->shouldNotHaveBeenCalled();
    }
}
