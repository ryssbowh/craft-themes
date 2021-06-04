<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\CategoryRenderedOptions;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\base\Model;
use craft\elements\Category;
use craft\fields\Categories;

class CategoryRendered extends FieldDisplayer
{
    public static $handle = 'category_rendered';

    public $hasOptions = true;

    private $_layout;

    public function getName(): string
    {
        return \Craft::t('themes', 'Rendered');
    }

    public static function getFieldTarget(): String
    {
        return Categories::class;
    }

    public function getOptionsModel(): Model
    {
        return new CategoryRenderedOptions;
    }

    public function getGroupLayout(): Layout
    {
        $group = $this->getCategoryGroup();
        $theme = Themes::$plugin->registry->getCurrent();
        $layout = Themes::$plugin->layouts->get($theme->handle, LayoutService::CATEGORY_HANDLE, $group->uid);
        $layout->setDisplaysRenderingMode();
        return $layout;
    }

    public function getViewModes(): array
    {
        $group = $this->getCategoryGroup();
        $layout = Themes::$plugin->layouts->get($this->getTheme(), LayoutService::CATEGORY_HANDLE, $group->uid);
        $viewModes = [];
        foreach ($layout->getViewModes() as $viewMode) {
            $viewModes[$viewMode->uid] = $viewMode->name;
        }
        return $viewModes;
    }

    public function fields()
    {
        return array_merge(parent::fields(), ['viewModes']);
    }

    protected function getCategoryGroup()
    {
        $elems = explode(':', $this->field->craftField->source);
        return \Craft::$app->categories->getGroupByUid($elems[1]);
    }
}