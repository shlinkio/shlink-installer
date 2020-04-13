<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Worker;

use Shlinkio\Shlink\Installer\Config\Option\Worker\AbstractWorkerNumConfigOption;

class TaskWorkerNumConfigOption extends AbstractWorkerNumConfigOption
{
    public function getConfigPath(): array
    {
        return ['task_worker_num'];
    }

    protected function getQuestionToAsk(): string
    {
        return 'How many concurrent background tasks do you want Shlink to be able to execute?';
    }
}
