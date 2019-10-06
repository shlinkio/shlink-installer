<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Factory;

use Symfony\Component\Console\Helper;

class ProcessHelperFactory
{
    public function __invoke(): Helper\ProcessHelper
    {
        $processHelper = new Helper\ProcessHelper();
        $processHelper->setHelperSet(new Helper\HelperSet([
            new Helper\FormatterHelper(),
            new Helper\DebugFormatterHelper(),
        ]));

        return $processHelper;
    }
}
