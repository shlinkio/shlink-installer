<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Worker;

class WebWorkerNumConfigOption extends AbstractWorkerNumConfigOption
{
    public function getEnvVar(): string
    {
        return 'WEB_WORKER_NUM';
    }

    protected function getQuestionToAsk(): string
    {
        return 'How many concurrent requests do you want Shlink to be able to serve?';
    }
}
