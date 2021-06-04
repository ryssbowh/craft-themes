<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\CategoryListOptions;
use craft\base\Model;
use craft\fields\Categories;

class CategoryList extends FieldDisplayer
{
    public static $handle = 'category_list';

    public $hasOptions = true;

    public static $isDefault = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'List');
    }

    public static function getFieldTarget(): String
    {
        return Categories::class;
    }

    public function getOptionsModel(): Model
    {
        return new CategoryListOptions;
    }
}