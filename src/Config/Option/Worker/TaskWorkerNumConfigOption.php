<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Worker;

class TaskWorkerNumConfigOption extends AbstractWorkerNumConfigOption
{
    public function getEnvVar(): string
    {
        return 'TASK_WORKER_NUM';
    }

    protected function getQuestionToAsk(): string
    {
        return 'How many concurrent background tasks do you want Shlink to be able to execute?';
    }

    protected function getMinimumValue(): int
    {
        return 4;
    }
}
