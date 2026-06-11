<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Config\Util;

enum RuntimeType: string
{
    case ASYNC = 'async';
    case REGULAR = 'regular';
}
