<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\DateOptions;
use craft\base\Model;
use craft\fields\Date as DateField;

/**
 * Renders a date field
 */
class Date extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'date_date';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Date');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTarget(): String
    {
        return DateField::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return DateOptions::class;
    }
}