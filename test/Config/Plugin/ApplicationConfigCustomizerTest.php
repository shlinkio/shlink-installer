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

    public function setUp(): void
    {
        $this->io = $this->prophesize(SymfonyStyle::class);
        $this->io->title(Argument::any())->willReturn(null);

        $stringGenerator = $this->prophesize(StringGeneratorInterface::class);
        $stringGenerator->generateRandomString(32)->willReturn('the_secret');

        $this->plugin = new ApplicationConfigCustomizer(
            $this->createExpectedConfigResolverMock(),
            $stringGenerator->reveal()
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
            'SECRET' => 'the_secret',
            'DISABLE_TRACK_PARAM' => 'asked',
            'CHECK_VISITS_THRESHOLD' => false,
        ], $config->getApp());
        $ask->shouldHaveBeenCalledTimes(1);
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
            'SECRET' => 'the_secret',
            'DISABLE_TRACK_PARAM' => 'asked',
            'CHECK_VISITS_THRESHOLD' => true,
            'VISITS_THRESHOLD' => 20,
        ], $config->getApp());
        $ask->shouldHaveBeenCalledTimes(2);
        $confirm->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function onlyMissingOptionsAreAsked(): void
    {
        $ask = $this->io->ask(Argument::cetera())->willReturn('disable_param');
        $config = new CustomizableAppConfig();
        $config->setApp([
            'SECRET' => 'foo',
            'CHECK_VISITS_THRESHOLD' => true,
            'VISITS_THRESHOLD' => 20,
        ]);

        $this->plugin->process($this->io->reveal(), $config);

        $this->assertEquals([
            'SECRET' => 'foo',
            'DISABLE_TRACK_PARAM' => 'disable_param',
            'CHECK_VISITS_THRESHOLD' => true,
            'VISITS_THRESHOLD' => 20,
        ], $config->getApp());
        $ask->shouldHaveBeenCalledOnce();
    }

    /** @test */
    public function noQuestionsAskedIfImportedConfigContainsEverything(): void
    {
        $ask = $this->io->ask(Argument::cetera())->willReturn('the_new_secret');

        $config = new CustomizableAppConfig();
        $config->setApp([
            'SECRET' => 'foo',
            'DISABLE_TRACK_PARAM' => 'the_new_secret',
            'CHECK_VISITS_THRESHOLD' => true,
            'VISITS_THRESHOLD' => 20,
        ]);

        $this->plugin->process($this->io->reveal(), $config);

        $this->assertEquals([
            'SECRET' => 'foo',
            'DISABLE_TRACK_PARAM' => 'the_new_secret',
            'CHECK_VISITS_THRESHOLD' => true,
            'VISITS_THRESHOLD' => 20,
        ], $config->getApp());
        $ask->shouldNotHaveBeenCalled();
    }

    /**
     * @test
     * @dataProvider provideInvalidValues
     * @param mixed $value
     */
    public function validateVisitsThresholdThrowsExceptionWhenProvidedValueIsInvalid($value): void
    {
        $this->expectException(InvalidConfigOptionException::class);
        $this->plugin->validateVisitsThreshold($value);
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
    public function validateVisitsThresholdCastsToIntWhenProvidedValueIsValid($value, int $expected): void
    {
        $this->assertEquals($expected, $this->plugin->validateVisitsThreshold($value));
    }

    public function provideValidValues(): iterable
    {
        yield 'positive as string' => ['20', 20];
        yield 'positive as integer' => [5, 5];
        yield 'one as string' => ['1', 1];
        yield 'one as integer' => [1, 1];
    }
}
