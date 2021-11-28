<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\DateTimeOptions;
use craft\base\Model;
use craft\fields\Date;

/**
 * Renders a date field
 */
class DateTime extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'date_datetime';

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

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
    public static function getFieldTarget(): String
    {
        return Date::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return DateTimeOptions::class;
    }
}