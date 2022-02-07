<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\helpers\ViewModesHelper;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\CategoryRenderedOptions;
use craft\fields\Categories;

/**
 * Renders a category field as rendered using a view mode
 */
class CategoryRendered extends CategoryLabel
{
    /**
     * @inheritDoc
     */
    public static $handle = 'category-rendered';

    /**
     * @inheritDoc
     */
    public static function isDefault(string $fieldClass): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Rendered as view mode');
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(array $eagerLoad, string $prefix = '', int $level = 0): array
    {
        foreach ($this->getViewModes() as $uid => $label) {
            $viewMode = Themes::$plugin->viewModes->getByUid($uid);
            //Avoid infinite loops for self referencing view modes :
            if ($viewMode->id != $this->field->viewMode->id) {
                $eagerLoad = array_merge($eagerLoad, $viewMode->eagerLoad($prefix, $level));
            }
        }
        return $eagerLoad;
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [Categories::class];
    }

    /**
     * Get view modes available, based on the field category
     * 
     * @return array
     */
    public function getViewModes(): array
    {
        return ViewModesHelper::getCategoryGroupViewModes($this->field->craftField, $this->getTheme());
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return CategoryRenderedOptions::class;
    }
}