<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\TitleDefaultOptions;
use Ryssbowh\CraftThemes\models\fields\Title;
use craft\base\Model;

class TitleDefault extends FieldDisplayer
{
    public static $handle = 'title_default';

    public static $isDefault = true;

    public $hasOptions = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public static function getFieldTarget(): String
    {
        return Title::class;
    }

    public function eagerLoad(): array
    {
        return [];
    }

    public function getOptionsModel(): Model
    {
        return new TitleDefaultOptions;
    }
}