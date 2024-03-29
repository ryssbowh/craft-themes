<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\DateTimeOptions;

/**
 * Renders a date field
 */
class DateTime extends Date
{
    /**
     * @inheritDoc
     */
    public static $handle = 'date-datetime';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Date and time');
    }

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
    protected function getOptionsModel(): string
    {
        return DateTimeOptions::class;
    }
}