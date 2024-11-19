<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

use Webimpress\SafeWriter\FileWriter;

use function sprintf;
use function var_export;

class ConfigWriter implements ConfigWriterInterface
{
    private const CONFIG_TEMPLATE = <<<TEMPLATE
    <?php
    
    /* Shlink config generated by shlink-installer */
    
    return %s;
    TEMPLATE;


    public function toFile(string $fileName, array $config): void
    {
        $content = sprintf(self::CONFIG_TEMPLATE, var_export($config, return: true));
        FileWriter::writeFile($fileName, $content);
    }
}
