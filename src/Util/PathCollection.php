<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Installer\Util;

use Shlinkio\Shlink\Config\Collection\PathCollection as ConfigPathCollection;

/** @deprecated Use Shlinkio\Shlink\Config\Collection\PathCollection instead */
final class PathCollection
{
    private ConfigPathCollection $wrappedCollection;

    public function __construct(array $array = [])
    {
        $this->wrappedCollection = new ConfigPathCollection($array);
    }

    public function pathExists(array $path): bool
    {
        return $this->wrappedCollection->pathExists($path);
    }

    /**
     * @return mixed
     */
    public function getValueInPath(array $path)
    {
        return $this->wrappedCollection->getValueInPath($path);
    }

    /**
     * @param mixed $value
     */
    public function setValueInPath($value, array $path): void
    {
        $this->wrappedCollection->setValueInPath($value, $path);
    }

    public function toArray(): array
    {
        return $this->wrappedCollection->toArray();
    }
}
