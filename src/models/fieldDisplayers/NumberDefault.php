<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\NumberDefaultOptions;
use craft\base\Model;
use craft\fields\Number;

class NumberDefault extends FieldDisplayer
{
    public static $handle = 'number_default';

    public static $isDefault = true;

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public static function getFieldTarget(): String
    {
        return Number::class;
    }

    public function getOptionsModel(): Model
    {
        return new NumberDefaultOptions;
    }
}