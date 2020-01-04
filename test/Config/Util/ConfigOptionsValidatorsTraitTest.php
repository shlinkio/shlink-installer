<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Util;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Util\ConfigOptionsValidatorsTrait;
use Shlinkio\Shlink\Installer\Exception\InvalidConfigOptionException;

class ConfigOptionsValidatorsTraitTest extends TestCase
{
    private object $validators;

    protected function setUp(): void
    {
        $this->validators = new class {
            use ConfigOptionsValidatorsTrait;
        };
    }

    /**
     * @test
     * @dataProvider provideValidUrls
     */
    public function webhooksAreProperlySplitAndValidated(?string $webhooks, array $expectedResult): void
    {
        $result = $this->validators->splitAndValidateMultipleUrls($webhooks);
        $this->assertEquals($expectedResult, $result);
    }

    public function provideValidUrls(): iterable
    {
        yield 'no webhooks' => [null, []];
        yield 'single webhook' => ['https://foo.com/bar', ['https://foo.com/bar']];
        yield 'multiple webhook' => ['https://foo.com/bar,http://bar.io/foo/bar', [
            'https://foo.com/bar',
            'http://bar.io/foo/bar',
        ]];
    }

    /**
     * @test
     * @dataProvider provideInvalidUrls
     */
    public function webhooksFailWhenProvidedValueIsNotValidUrl(string $webhooks): void
    {
        $this->expectException(InvalidConfigOptionException::class);
        $this->validators->splitAndValidateMultipleUrls($webhooks);
    }

    public function provideInvalidUrls(): iterable
    {
        yield 'single invalid webhook' => ['invalid'];
        yield 'first invalid webhook' => ['invalid,http://bar.io/foo/bar'];
        yield 'last invalid webhook' => ['http://bar.io/foo/bar,invalid'];
        yield 'middle invalid webhook' => ['http://bar.io/foo/bar,invalid,https://foo.com/bar'];
    }

    /** @test */
    public function throwsAnExceptionIfInvalidUrlIsProvided(): void
    {
        $this->expectException(InvalidConfigOptionException::class);
        $this->expectExceptionMessage('Provided value "something" is not a valid URL');

        $this->validators->validateUrl('something');
    }

    /**
     * @test
     * @dataProvider provideInvalidValues
     * @param mixed $value
     */
    public function validatePositiveNumberThrowsExceptionWhenProvidedValueIsInvalid($value): void
    {
        $this->expectException(InvalidConfigOptionException::class);
        $this->validators->validatePositiveNumber($value);
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
        $this->assertEquals($expected, $this->validators->validatePositiveNumber($value));
    }

    public function provideValidValues(): iterable
    {
        yield 'positive as string' => ['20', 20];
        yield 'positive as integer' => [5, 5];
        yield 'one as string' => ['1', 1];
        yield 'one as integer' => [1, 1];
    }
}
