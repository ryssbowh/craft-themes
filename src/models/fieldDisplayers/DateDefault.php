<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\DateDefaultOptions;
use craft\base\Model;
use craft\fields\Date;

class DateDefault extends FieldDisplayer
{
    public static $handle = 'date_default';

    public $hasOptions = true;

    public static $isDefault = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public static function getFieldTarget(): String
    {
        return Date::class;
    }

    public function getOptionsModel(): Model
    {
        return new DateDefaultOptions;
    }
}