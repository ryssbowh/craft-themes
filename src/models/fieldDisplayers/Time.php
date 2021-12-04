<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\TimeOptions;
use craft\fields\Time as TimeField;

/**
 * Renders a time field
 */
class Time extends Date
{
    /**
     * @inheritDoc
     */
    public static $handle = 'time';

    /**
     * @inheritDoc
     */
    public static function isDefault(string $fieldClass): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [TimeField::class];
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Time');
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return TimeOptions::class;
    }
}