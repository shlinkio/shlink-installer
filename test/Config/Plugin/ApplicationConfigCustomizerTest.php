<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Plugin;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Shlinkio\Shlink\Installer\Config\Plugin\ApplicationConfigCustomizer;
use Shlinkio\Shlink\Installer\Exception\InvalidConfigOptionException;
use Shlinkio\Shlink\Installer\Model\CustomizableAppConfig;
use Shlinkio\Shlink\Installer\Util\StringGeneratorInterface;
use ShlinkioTest\Shlink\Installer\Util\TestUtilsTrait;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_shift;
use function strpos;

class ApplicationConfigCustomizerTest extends TestCase
{
    use TestUtilsTrait;

    /** @var ApplicationConfigCustomizer */
    private $plugin;
    /** @var ObjectProphecy */
    private $io;
    /** @var bool */
    private $swooleEnabled;

    public function setUp(): void
    {
        $this->swooleEnabled = false;

        $this->io = $this->prophesize(SymfonyStyle::class);
        $this->io->title(Argument::any())->willReturn(null);

        $stringGenerator = $this->prophesize(StringGeneratorInterface::class);
        $stringGenerator->generateRandomString(32)->willReturn('the_secret');

        $this->plugin = new ApplicationConfigCustomizer(
            $this->createExpectedConfigResolverMock(),
            $stringGenerator->reveal(),
            function () {
                return $this->swooleEnabled;
            }
        );
    }

    /** @test */
    public function configIsRequestedToTheUser(): void
    {
        $ask = $this->io->ask(Argument::cetera())->willReturn('asked');
        $confirm = $this->io->confirm(Argument::cetera())->willReturn(false);

        $config = new CustomizableAppConfig();

        $this->plugin->process($this->io->reveal(), $config);

        $this->assertTrue($config->hasApp());
        $this->assertEquals([
            ApplicationConfigCustomizer::SECRET => 'the_secret',
            ApplicationConfigCustomizer::DISABLE_TRACK_PARAM => 'asked',
            ApplicationConfigCustomizer::CHECK_VISITS_THRESHOLD => false,
            ApplicationConfigCustomizer::BASE_PATH => 'asked',
        ], $config->getApp());
        $ask->shouldHaveBeenCalledTimes(2);
        $confirm->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function visitsThresholdIsRequestedIfCheckIsEnabled(): void
    {
        $ask = $this->io->ask(Argument::cetera())->will(function (array $args) {
            $message = array_shift($args);
            return strpos($message, 'What is the amount of visits') === 0 ? 20 : 'asked';
        });
        $confirm = $this->io->confirm(Argument::cetera())->willReturn(true);

        $config = new CustomizableAppConfig();

        $this->plugin->process($this->io->reveal(), $config);

        $this->assertTrue($config->hasApp());
        $this->assertEquals([
            ApplicationConfigCustomizer::SECRET => 'the_secret',
            ApplicationConfigCustomizer::DISABLE_TRACK_PARAM => 'asked',
            ApplicationConfigCustomizer::CHECK_VISITS_THRESHOLD => true,
            ApplicationConfigCustomizer::VISITS_THRESHOLD => 20,
            ApplicationConfigCustomizer::BASE_PATH => 'asked',
        ], $config->getApp());
        $ask->shouldHaveBeenCalledTimes(3);
        $confirm->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function onlyMissingOptionsAreAsked(): void
    {
        $ask = $this->io->ask(Argument::cetera())->willReturn('disable_param');
        $config = new CustomizableAppConfig();
        $config->setApp([
            ApplicationConfigCustomizer::SECRET => 'foo',
            ApplicationConfigCustomizer::CHECK_VISITS_THRESHOLD => true,
            ApplicationConfigCustomizer::VISITS_THRESHOLD => 20,
            ApplicationConfigCustomizer::BASE_PATH => '/foo/bar',
        ]);

        $this->plugin->process($this->io->reveal(), $config);

        $this->assertEquals([
            ApplicationConfigCustomizer::SECRET => 'foo',
            ApplicationConfigCustomizer::DISABLE_TRACK_PARAM => 'disable_param',
            ApplicationConfigCustomizer::CHECK_VISITS_THRESHOLD => true,
            ApplicationConfigCustomizer::VISITS_THRESHOLD => 20,
            ApplicationConfigCustomizer::BASE_PATH => '/foo/bar',
        ], $config->getApp());
        $ask->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function noQuestionsAskedIfImportedConfigContainsEverything(): void
    {
        $ask = $this->io->ask(Argument::cetera())->willReturn('the_new_secret');

        $config = new CustomizableAppConfig();
        $config->setApp([
            ApplicationConfigCustomizer::SECRET => 'foo',
            ApplicationConfigCustomizer::DISABLE_TRACK_PARAM => 'the_new_secret',
            ApplicationConfigCustomizer::CHECK_VISITS_THRESHOLD => true,
            ApplicationConfigCustomizer::VISITS_THRESHOLD => 20,
            ApplicationConfigCustomizer::BASE_PATH => '/foo/bar',
        ]);

        $this->plugin->process($this->io->reveal(), $config);

        $this->assertEquals([
            ApplicationConfigCustomizer::SECRET => 'foo',
            ApplicationConfigCustomizer::DISABLE_TRACK_PARAM => 'the_new_secret',
            ApplicationConfigCustomizer::CHECK_VISITS_THRESHOLD => true,
            ApplicationConfigCustomizer::VISITS_THRESHOLD => 20,
            ApplicationConfigCustomizer::BASE_PATH => '/foo/bar',
        ], $config->getApp());
        $ask->shouldNotHaveBeenCalled();
    }

    /** @test */
    public function swooleConfigIsIncludedWhenSwooleIsLoaded(): void
    {
        $this->swooleEnabled = true;

        $ask = $this->io->ask(Argument::cetera())->willReturn('asked');
        $config = new CustomizableAppConfig();
        $config->setApp([
            ApplicationConfigCustomizer::SECRET => 'foo',
            ApplicationConfigCustomizer::CHECK_VISITS_THRESHOLD => true,
            ApplicationConfigCustomizer::VISITS_THRESHOLD => 20,
            ApplicationConfigCustomizer::BASE_PATH => '/foo/bar',
        ]);

        $this->plugin->process($this->io->reveal(), $config);

        $this->assertEquals([
            ApplicationConfigCustomizer::SECRET => 'foo',
            ApplicationConfigCustomizer::DISABLE_TRACK_PARAM => 'asked',
            ApplicationConfigCustomizer::CHECK_VISITS_THRESHOLD => true,
            ApplicationConfigCustomizer::VISITS_THRESHOLD => 20,
            ApplicationConfigCustomizer::BASE_PATH => '/foo/bar',
            ApplicationConfigCustomizer::WEB_WORKER_NUM => 'asked',
            ApplicationConfigCustomizer::TASK_WORKER_NUM => 'asked',
        ], $config->getApp());
        $ask->shouldHaveBeenCalledTimes(3);
    }

    /**
     * @test
     * @dataProvider provideInvalidValues
     * @param mixed $value
     */
    public function validatePositiveNumberThrowsExceptionWhenProvidedValueIsInvalid($value): void
    {
        $this->expectException(InvalidConfigOptionException::class);
        $this->plugin->validatePositiveNumber($value);
    }

    public function provideInvalidValues(): iterable
    {
        yield 'string' => ['foo'];
        yield 'empty string' => [''];
        yield 'negative number' => [-5];
        yield 'negative number as string' => ['-5'];
        yield 'zero' => [0];
        yield 'zero as string' => ['0'];
    }

    /**
     * @test
     * @dataProvider provideValidValues
     * @param mixed $value
     */
    public function validatePositiveNumberCastsToIntWhenProvidedValueIsValid($value, int $expected): void
    {
        $this->assertEquals($expected, $this->plugin->validatePositiveNumber($value));
    }

    public function provideValidValues(): iterable
    {
        yield 'positive as string' => ['20', 20];
        yield 'positive as integer' => [5, 5];
        yield 'one as string' => ['1', 1];
        yield 'one as integer' => [1, 1];
    }
}
