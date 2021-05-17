<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\FileDefaultOptions;
use Ryssbowh\CraftThemes\models\fields\File;
use craft\base\Model;

class FileDefault extends FieldDisplayer
{
    public static $handle = 'file_default';

    public $hasOptions = true;

    public static $isDefault = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public static function getFieldTarget(): String
    {
        return File::class;
    }

    public function getOptionsModel(): Model
    {
        return new FileDefaultOptions;
    }
}