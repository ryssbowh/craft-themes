<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\CategoryRenderedOptions;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\base\Model;
use craft\elements\Category;
use craft\fields\Categories;
use craft\models\CategoryGroup;

class CategoryRendered extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'category_rendered';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Rendered');
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
        return CategoryRenderedOptions::class;
    }

    /**
     * Get the layout associated to this displayer field's category group
     * 
     * @return LayoutInterface
     */
    public function getGroupLayout(): ?LayoutInterface
    {
        if ($group = $this->getCategoryGroup()) {
            $theme = Themes::$plugin->registry->getCurrent();
            return Themes::$plugin->layouts->get($theme, LayoutService::CATEGORY_HANDLE, $group->uid);
        }
        return null;
    }

    /**
     * Get view modes associated to this displayer field's category group
     * 
     * @return array
     */
    public function getViewModes(): array
    {
        $viewModes = [];
        if ($group = $this->getCategoryGroup()) {
            $layout = Themes::$plugin->layouts->get($this->getTheme(), LayoutService::CATEGORY_HANDLE, $group->uid);
            foreach ($layout->getViewModes() as $viewMode) {
                $viewModes[$viewMode->uid] = $viewMode->name;
            }
        }
        return $viewModes;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['viewModes']);
    }

    /**
     * get the category group defined on this displayer's field
     * 
     * @return CategoryGroup
     */
    protected function getCategoryGroup(): ?CategoryGroup
    {
        $elems = explode(':', $this->field->craftField->source);
        return \Craft::$app->categories->getGroupByUid($elems[1]);
    }
}