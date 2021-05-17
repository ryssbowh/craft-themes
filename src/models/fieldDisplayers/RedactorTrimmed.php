<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\RedactorTrimmedOptions;
use craft\base\Model;
use craft\redactor\Field;

class RedactorTrimmed extends FieldDisplayer
{
    public static $handle = 'redactor_trimmed';

    public static $isDefault = true;

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Trimmed');
    }

    public static function getFieldTarget(): String
    {
        return Field::class;
    }

    public function getOptionsModel(): Model
    {
        return new RedactorTrimmedOptions;
    }
}