<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\RedactorFullOptions;
use craft\base\Model;
use craft\redactor\Field;

class RedactorFull extends FieldDisplayer
{
    public static $handle = 'redactor_full';

    public static $isDefault = true;

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Full');
    }

    public static function getFieldTarget(): String
    {
        return Field::class;
    }

    public function getOptionsModel(): Model
    {
        return new RedactorFullOptions;
    }
}