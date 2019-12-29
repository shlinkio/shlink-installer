<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Plugin;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\Installer\Config\Plugin\UrlShortenerConfigCustomizer;
use Shlinkio\Shlink\Installer\Model\CustomizableAppConfig;
use ShlinkioTest\Shlink\Installer\Util\TestUtilsTrait;
use Symfony\Component\Console\Style\SymfonyStyle;

class UrlShortenerConfigCustomizerTest extends TestCase
{
    use TestUtilsTrait;

    /** @var UrlShortenerConfigCustomizer */
    private $plugin;
    /** @var ObjectProphecy */
    private $io;

    public function setUp(): void
    {
        $this->io = $this->prophesize(SymfonyStyle::class);
        $this->io->title(Argument::any())->willReturn(null);

        $this->plugin = new UrlShortenerConfigCustomizer($this->createExpectedConfigResolverMock());
    }

    /** @test */
    public function configIsRequestedToTheUser(): void
    {
        $choice = $this->io->choice(Argument::cetera())->willReturn('chosen');
        $ask = $this->io->ask(Argument::cetera())->willReturn('asked');
        $confirm = $this->io->confirm(Argument::cetera())->willReturn(true);
        $config = new CustomizableAppConfig();

        $this->plugin->process($this->io->reveal(), $config);

        $this->assertTrue($config->hasUrlShortener());
        $this->assertEquals([
            UrlShortenerConfigCustomizer::SCHEMA => 'chosen',
            UrlShortenerConfigCustomizer::HOSTNAME => 'asked',
            UrlShortenerConfigCustomizer::VALIDATE_URL => true,
            UrlShortenerConfigCustomizer::NOTIFY_VISITS_WEBHOOKS => true,
            UrlShortenerConfigCustomizer::VISITS_WEBHOOKS => 'asked',
        ], $config->getUrlShortener());
        $ask->shouldHaveBeenCalledTimes(2);
        $choice->shouldHaveBeenCalledOnce();
        $confirm->shouldHaveBeenCalledTimes(2);
    }

    /** @test */
    public function onlyMissingOptionsAreAsked(): void
    {
        $choice = $this->io->choice(Argument::cetera())->willReturn('chosen');
        $ask = $this->io->ask(Argument::cetera())->willReturn('asked');
        $confirm = $this->io->confirm(Argument::cetera())->willReturn(false);
        $config = new CustomizableAppConfig();
        $config->setUrlShortener([
            UrlShortenerConfigCustomizer::SCHEMA => 'foo',
            UrlShortenerConfigCustomizer::NOTIFY_VISITS_WEBHOOKS => false,
        ]);

        $this->plugin->process($this->io->reveal(), $config);

        $this->assertEquals([
            UrlShortenerConfigCustomizer::SCHEMA => 'foo',
            UrlShortenerConfigCustomizer::HOSTNAME => 'asked',
            UrlShortenerConfigCustomizer::VALIDATE_URL => false,
            UrlShortenerConfigCustomizer::NOTIFY_VISITS_WEBHOOKS => false,
        ], $config->getUrlShortener());
        $choice->shouldNotHaveBeenCalled();
        $ask->shouldHaveBeenCalledOnce();
        $confirm->shouldHaveBeenCalledOnce();
    }

    /**
     * @test
     * @dataProvider provideWholeConfig
     */
    public function noQuestionsAskedIfImportedConfigContainsEverything(array $urlShortenerConfig): void
    {
        $choice = $this->io->choice(Argument::cetera())->willReturn('chosen');
        $ask = $this->io->ask(Argument::cetera())->willReturn('asked');
        $confirm = $this->io->confirm(Argument::cetera())->willReturn(false);

        $config = new CustomizableAppConfig();
        $config->setUrlShortener($urlShortenerConfig);

        $this->plugin->process($this->io->reveal(), $config);

        $this->assertEquals($urlShortenerConfig, $config->getUrlShortener());
        $choice->shouldNotHaveBeenCalled();
        $ask->shouldNotHaveBeenCalled();
        $confirm->shouldNotHaveBeenCalled();
    }

    public function provideWholeConfig(): iterable
    {
        yield [[
            UrlShortenerConfigCustomizer::SCHEMA => 'foo',
            UrlShortenerConfigCustomizer::HOSTNAME => 'foo',
            UrlShortenerConfigCustomizer::VALIDATE_URL => true,
            UrlShortenerConfigCustomizer::NOTIFY_VISITS_WEBHOOKS => false,
        ]];
        yield [[
            UrlShortenerConfigCustomizer::SCHEMA => 'foo',
            UrlShortenerConfigCustomizer::HOSTNAME => 'foo',
            UrlShortenerConfigCustomizer::VALIDATE_URL => true,
            UrlShortenerConfigCustomizer::NOTIFY_VISITS_WEBHOOKS => true,
            UrlShortenerConfigCustomizer::VISITS_WEBHOOKS => 'webhook',
        ]];
    }
}
