<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Model;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class FlagOption
{
    public function __construct(Command $command, private readonly string $name, string $description)
    {
        $command->addOption($name, null, InputOption::VALUE_NONE, $description);
    }

    public function get(InputInterface $input): bool
    {
        return $input->getOption($this->name);
    }
}
