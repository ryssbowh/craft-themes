<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\TimeDefaultOptions;
use craft\base\Model;
use craft\fields\Time;

class TimeDefault extends FieldDisplayer
{
    public static $handle = 'time_default';

    public $hasOptions = true;

    public static $isDefault = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public static function getFieldTarget(): String
    {
        return Time::class;
    }

    public function getOptionsModel(): Model
    {
        return new TimeDefaultOptions;
    }
}