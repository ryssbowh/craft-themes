<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\displayerOptions\CategoryRenderedOptions;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use craft\base\Model;
use craft\elements\Category;
use craft\fields\Categories;

class CategoryRendered extends FieldDisplayer
{
    public $handle = 'category_rendered';

    public $hasOptions = true;

    private $_layout;

    public function getName(): string
    {
        return \Craft::t('themes', 'Rendered');
    }

    public function getFieldTarget(): String
    {
        return Categories::class;
    }

    public function getOptionsModel(): Model
    {
        return new CategoryRenderedOptions;
    }

    public function getOptionsHtml(): string
    {
        return \Craft::$app->view->renderTemplate('themes/cp/displayer-options/' . $this->handle, [
            'options' => $this->getOptions()
        ]);
    }

    protected function getCategoryGroup()
    {
        $elems = explode(':', $this->field->craftField()->source);
        return \Craft::$app->categories->getGroupByUid($elems[1]);
    }

    public function getGroupLayout(): Layout
    {
        $group = $this->getCategoryGroup();
        $theme = Themes::$plugin->registry->getCurrent();
        $layout = Themes::$plugin->layouts->get($theme->handle, $group->uid);
        $layout->setDisplaysRenderingMode();
        return $layout;
    }
}