<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\RedactorTruncatedOptions;
use craft\base\Model;
use craft\redactor\Field;

class RedactorTruncated extends FieldDisplayer
{
    public static $handle = 'redactor_truncated';

    public static $isDefault = true;

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Truncated');
    }

    public static function getFieldTarget(): String
    {
        return Field::class;
    }

    public function getOptionsModel(): Model
    {
        return new RedactorTruncatedOptions;
    }
}