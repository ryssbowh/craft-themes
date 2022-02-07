<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\TimeAgoOptions;

/**
 * Renders a date field as time ago
 */
class TimeAgo extends Date
{
    /**
     * @inheritDoc
     */
    public static $handle = 'time-ago';

    /**
     * @inheritDoc
     */
    public static function isDefault(string $fieldClass): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Time ago');
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return TimeAgoOptions::class;
    }
}