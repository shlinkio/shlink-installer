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
        self::assertEquals($expectedResult, $result);
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
     */
    public function validateNumberGreaterThanThrowsExceptionWhenProvidedValueIsInvalid(array $args): void
    {
        $this->expectException(InvalidConfigOptionException::class);
        $this->validators->validateNumberGreaterThan(...$args);
    }

    public function provideInvalidValues(): iterable
    {
        yield 'string' => [['foo', 1]];
        yield 'empty string' => [['', 1]];
        yield 'negative number' => [[-5, 1]];
        yield 'negative number as string' => [['-5', 1]];
        yield 'zero' => [[0, 1]];
        yield 'zero as string' => [['0', 1]];
        yield 'null' => [[null, 1]];
        yield 'positive with min' => [[5, 6]];
    }

    /**
     * @test
     * @dataProvider providePositiveNumbers
     */
    public function validatePositiveNumberCastsToIntWhenProvidedValueIsValid(mixed $value, int $expected): void
    {
        self::assertEquals($expected, $this->validators->validatePositiveNumber($value));
    }

    public function providePositiveNumbers(): iterable
    {
        yield 'positive as string' => ['20', 20];
        yield 'positive as integer' => [5, 5];
        yield 'one as string' => ['1', 1];
        yield 'one as integer' => [1, 1];
    }

    /**
     * @test
     * @dataProvider provideOptionalPositiveNumbers
     */
    public function validateOptionalPositiveNumberCastsToIntWhenProvidedValueIsValid(mixed $value, ?int $expected): void
    {
        self::assertEquals($expected, $this->validators->validateOptionalPositiveNumber($value));
    }

    public function provideOptionalPositiveNumbers(): iterable
    {
        yield 'null' => [null, null];
        yield from $this->providePositiveNumbers();
    }

    /**
     * @test
     * @dataProvider provideInvalidNumbersBetween
     */
    public function validateNumberBetweenThrowsExceptionWhenProvidedValueIsInvalid(
        mixed $value,
        int $min,
        int $max,
    ): void {
        $this->expectException(InvalidConfigOptionException::class);
        $this->validators->validateNumberBetween($value, $min, $max);
    }

    public function provideInvalidNumbersBetween(): iterable
    {
        yield 'string' => ['foo', 1, 2];
        yield 'lower as int' => [10, 20, 30];
        yield 'lower as string' => ['10', 20, 30];
        yield 'right before as int' => [19, 20, 30];
        yield 'right before as string' => ['19', 20, 30];
        yield 'right after as int' => [31, 20, 30];
        yield 'right after as string' => ['31', 20, 30];
        yield 'greater as int' => [50, 20, 30];
        yield 'greater as string' => ['300', 20, 30];
        yield 'impossible range' => [15, 30, 20];
    }

    /**
     * @test
     * @dataProvider provideValidNumbersBetween
     */
    public function validateNumberBetweenCastsToIntWhenProvidedValueIsValid(
        mixed $value,
        int $min,
        int $max,
        int $expected,
    ): void {
        self::assertEquals($expected, $this->validators->validateNumberBetween($value, $min, $max));
    }

    public function provideValidNumbersBetween(): iterable
    {
        yield 'first as string' => ['20', 20, 30, 20];
        yield 'first as int' => [20, 20, 30, 20];
        yield 'between as string' => ['30', 20, 40, 30];
        yield 'between as int' => [25, 20, 40, 25];
        yield 'last as string' => ['55', 20, 55, 55];
        yield 'last as int' => [55, 20, 55, 55];
    }
}
