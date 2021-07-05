<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\DateDefaultOptions;
use craft\base\Model;
use craft\fields\Date;

class DateDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'date_default';

    /**
     * @inheritDoc
     */
    public $hasOptions = true;

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
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
        return DateDefaultOptions::class;
    }
}