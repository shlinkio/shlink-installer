<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Option\Worker;

use Shlinkio\Shlink\Installer\Config\Option\Worker\AbstractWorkerNumConfigOption;

class WebWorkerNumConfigOption extends AbstractWorkerNumConfigOption
{
    public function getConfigPath(): array
    {
        return ['web_worker_num'];
    }

    protected function getQuestionToAsk(): string
    {
        return 'How many concurrent requests do you want Shlink to be able to serve?';
    }
}
