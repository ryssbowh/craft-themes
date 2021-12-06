<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

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
    public static $handle = 'category_rendered';

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
    public static function getFieldTargets(): array
    {
        return [Categories::class];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return CategoryRenderedOptions::class;
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
     * Get the layout associated to this displayer field's category group
     * 
     * @return LayoutInterface
     */
    public function getGroupLayout(): ?LayoutInterface
    {
        $elems = explode(':', $this->field->craftField->source);
        $group = \Craft::$app->categories->getGroupByUid($elems[1]);
        if ($group) {
            return $group->getLayout();
        }
        return null;
    }
}