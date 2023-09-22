<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Model;

use Shlinkio\Shlink\Installer\Command\Model\InitOption;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

class CLIOption
{
    public function __construct(Command $command, private readonly InitOption $initOption)
    {
        $command->addOption(
            $initOption->value,
            null,
            $initOption->valueType(),
            $initOption->description(),
            $this->initOption->defaultValue(),
        );
    }

    public function get(InputInterface $input): mixed
    {
        return $input->getOption($this->initOption->value);
    }
}
