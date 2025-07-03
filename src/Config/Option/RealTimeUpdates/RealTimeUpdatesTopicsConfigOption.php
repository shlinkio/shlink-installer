<?php

namespace Shlinkio\Shlink\Installer\Config\Option\RealTimeUpdates;

use Shlinkio\Shlink\Installer\Config\Option\BaseConfigOption;
use Symfony\Component\Console\Style\StyleInterface;

class RealTimeUpdatesTopicsConfigOption extends BaseConfigOption
{
    private const array TOPICS = [
        'NEW_VISIT' => 'Notify general non-orphan visits?',
        'NEW_SHORT_URL_VISIT' => 'Notify short-url visits? (A different topic per short URL will be created)',
        'NEW_ORPHAN_VISIT' => 'Notify orphan visits?',
        'NEW_SHORT_URL' => 'Notify created short URLs?',
    ];

    public function getEnvVar(): string
    {
        return 'REAL_TIME_UPDATES_TOPICS';
    }

    /**
     * @return string[]|null
     */
    public function ask(StyleInterface $io, array $currentOptions): array|null
    {
        $individualTopics = $io->confirm(
            'Do you want to enable individual real-time updates topics? (All topics will be enabled otherwise)',
            default: false,
        );
        if (! $individualTopics) {
            return null;
        }

        $topics = [];
        foreach (self::TOPICS as $topic => $question) {
            if ($io->confirm($question)) {
                $topics[] = $topic;
            }
        }

        return $topics;
    }
}
