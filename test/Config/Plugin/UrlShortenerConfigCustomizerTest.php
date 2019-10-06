<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Plugin;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\Installer\Config\Plugin\UrlShortenerConfigCustomizer;
use Shlinkio\Shlink\Installer\Model\CustomizableAppConfig;
use Shlinkio\Shlink\Installer\Util\StringGeneratorInterface;
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

        $stringGenerator = $this->prophesize(StringGeneratorInterface::class);
        $stringGenerator->generateRandomShortCodeChars()->willReturn('the_chars');

        $this->plugin = new UrlShortenerConfigCustomizer(
            $this->createExpectedConfigResolverMock(),
            $stringGenerator->reveal()
        );
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
            UrlShortenerConfigCustomizer::CHARS => 'the_chars',
            UrlShortenerConfigCustomizer::VALIDATE_URL => true,
            UrlShortenerConfigCustomizer::ENABLE_NOT_FOUND_REDIRECTION => true,
            UrlShortenerConfigCustomizer::NOT_FOUND_REDIRECT_TO => 'asked',
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
            UrlShortenerConfigCustomizer::ENABLE_NOT_FOUND_REDIRECTION => true,
            UrlShortenerConfigCustomizer::NOT_FOUND_REDIRECT_TO => 'foo',
        ]);

        $this->plugin->process($this->io->reveal(), $config);

        $this->assertEquals([
            UrlShortenerConfigCustomizer::SCHEMA => 'foo',
            UrlShortenerConfigCustomizer::HOSTNAME => 'asked',
            UrlShortenerConfigCustomizer::CHARS => 'the_chars',
            UrlShortenerConfigCustomizer::VALIDATE_URL => false,
            UrlShortenerConfigCustomizer::ENABLE_NOT_FOUND_REDIRECTION => true,
            UrlShortenerConfigCustomizer::NOT_FOUND_REDIRECT_TO => 'foo',
        ], $config->getUrlShortener());
        $choice->shouldNotHaveBeenCalled();
        $ask->shouldHaveBeenCalledOnce();
        $confirm->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function noQuestionsAskedIfImportedConfigContainsEverything(): void
    {
        $choice = $this->io->choice(Argument::cetera())->willReturn('chosen');
        $ask = $this->io->ask(Argument::cetera())->willReturn('asked');
        $confirm = $this->io->confirm(Argument::cetera())->willReturn(false);

        $config = new CustomizableAppConfig();
        $config->setUrlShortener([
            UrlShortenerConfigCustomizer::SCHEMA => 'foo',
            UrlShortenerConfigCustomizer::HOSTNAME => 'foo',
            UrlShortenerConfigCustomizer::CHARS => 'foo',
            UrlShortenerConfigCustomizer::VALIDATE_URL => true,
            UrlShortenerConfigCustomizer::ENABLE_NOT_FOUND_REDIRECTION => true,
            UrlShortenerConfigCustomizer::NOT_FOUND_REDIRECT_TO => 'foo',
        ]);

        $this->plugin->process($this->io->reveal(), $config);

        $this->assertEquals([
            UrlShortenerConfigCustomizer::SCHEMA => 'foo',
            UrlShortenerConfigCustomizer::HOSTNAME => 'foo',
            UrlShortenerConfigCustomizer::CHARS => 'foo',
            UrlShortenerConfigCustomizer::VALIDATE_URL => true,
            UrlShortenerConfigCustomizer::ENABLE_NOT_FOUND_REDIRECTION => true,
            UrlShortenerConfigCustomizer::NOT_FOUND_REDIRECT_TO => 'foo',
        ], $config->getUrlShortener());
        $choice->shouldNotHaveBeenCalled();
        $ask->shouldNotHaveBeenCalled();
        $confirm->shouldNotHaveBeenCalled();
    }

    /** @test */
    public function redirectUrlOptionIsNotAskedIfAnswerToPreviousQuestionIsNo(): void
    {
        $ask = $this->io->ask(Argument::cetera())->willReturn('asked');
        $confirm = $this->io->confirm(Argument::cetera())->willReturn(false);

        $config = new CustomizableAppConfig();
        $config->setUrlShortener([
            UrlShortenerConfigCustomizer::SCHEMA => 'foo',
            UrlShortenerConfigCustomizer::HOSTNAME => 'foo',
            UrlShortenerConfigCustomizer::CHARS => 'foo',
            UrlShortenerConfigCustomizer::VALIDATE_URL => true,
        ]);

        $this->plugin->process($this->io->reveal(), $config);

        $this->assertTrue($config->hasUrlShortener());
        $this->assertEquals([
            UrlShortenerConfigCustomizer::SCHEMA => 'foo',
            UrlShortenerConfigCustomizer::HOSTNAME => 'foo',
            UrlShortenerConfigCustomizer::CHARS => 'foo',
            UrlShortenerConfigCustomizer::VALIDATE_URL => true,
            UrlShortenerConfigCustomizer::ENABLE_NOT_FOUND_REDIRECTION => false,
        ], $config->getUrlShortener());
        $ask->shouldNotHaveBeenCalled();
        $confirm->shouldHaveBeenCalledOnce();
    }
}
