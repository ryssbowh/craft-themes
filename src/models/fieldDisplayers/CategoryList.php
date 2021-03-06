<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\CategoryListOptions;
use craft\base\Model;
use craft\fields\Categories;

class CategoryList extends FieldDisplayer
{
    public $handle = 'category_lists';

    public $hasOptions = true;

    public $name = 'List';

    public function getName()
    {
        return \Craft::t('themes', 'List');
    }

    public function getFieldTarget(): String
    {
        return Categories::class;
    }

    public function getOptionsModel(): ?Model
    {
        return new CategoryListOptions;
    }

    public function getOptionsHtml(): string
    {
        return \Craft::$app->view->renderTemplate('themes/cp/fieldDisplayers/category-list', [
            'options' => $this->getOptions()
        ]);
    }
}