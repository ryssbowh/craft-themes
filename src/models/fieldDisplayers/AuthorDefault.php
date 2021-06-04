<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\AuthorDefaultOptions;
use Ryssbowh\CraftThemes\models\fields\Author;
use craft\base\Model;

class AuthorDefault extends FieldDisplayer
{
    public static $handle = 'author_default';

    public $hasOptions = true;

    public static $isDefault = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public static function getFieldTarget(): String
    {
        return Author::class;
    }

    public function getOptionsModel(): Model
    {
        return new AuthorDefaultOptions;
    }
}