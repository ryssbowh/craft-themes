<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\CategoryListOptions;
use craft\base\Model;
use craft\fields\Categories;

class CategoryList extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'category_list';

    /**
     * @inheritDoc
     */
    public $hasOptions = true;

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'List');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTarget(): string
    {
        return Categories::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return CategoryListOptions::class;
    }
}