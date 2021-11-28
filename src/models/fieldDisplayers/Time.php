<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\TimeOptions;
use craft\base\Model;
use craft\fields\Time as TimeField;

/**
 * Renders a time field
 */
class Time extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'time_time';

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

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
    public static function getFieldTarget(): String
    {
        return TimeField::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return TimeOptions::class;
    }
}