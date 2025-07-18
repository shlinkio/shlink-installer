<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Installer\Config\Option;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Installer\Config\Option\TrustedProxiesConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class TrustedProxiesConfigOptionTest extends TestCase
{
    private TrustedProxiesConfigOption $configOption;

    public function setUp(): void
    {
        $this->configOption = new TrustedProxiesConfigOption();
    }

    #[Test]
    public function returnsExpectedEnvVar(): void
    {
        self::assertEquals('TRUSTED_PROXIES', $this->configOption->getEnvVar());
    }

    #[Test]
    public function nullIsReturnedWhenNoProxiesAreSet(): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('confirm')->with(
            'Do you have more than one proxy in front of this Shlink instance?',
            false,
        )->willReturn(false);
        $io->expects($this->never())->method('choice');
        $io->expects($this->never())->method('ask');

        self::assertNull($this->configOption->ask($io, []));
    }

    #[Test]
    #[TestWith(['amount', 'How many proxies do you have in front of Shlink?'])]
    #[TestWith([
        'list',
        'Provide a comma-separated list of your proxies\' IP addresses, CIDR blocks or wildcard '
        . 'addresses (1.2.*.*)',
    ])]
    public function expectedQuestionIsAskedBasedOnChoice(string $option, string $expectedAskedQuestion): void
    {
        $io = $this->createMock(StyleInterface::class);
        $io->expects($this->once())->method('confirm')->with(
            'Do you have more than one proxy in front of this Shlink instance?',
            false,
        )->willReturn(true);
        $io->expects($this->once())->method('choice')->with(
            'How do you want your proxies IP addresses to be identified, so that the visitor IP address can be '
            . 'properly determined?',
            [
                'amount' => 'Just set the amount of proxies',
                'list' => 'Define a comma-separated list of IP addresses, CIDR blocks or wildcard addresses (1.2.*.*)',
            ],
            'list',
        )->willReturn($option);

        $answer = '5';
        $io->expects($this->once())->method('ask')->with($expectedAskedQuestion, null, $this->isCallable())->willReturn(
            $answer,
        );

        self::assertEquals($answer, $this->configOption->ask($io, []));
    }
}
