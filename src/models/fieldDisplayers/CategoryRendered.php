<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\CategoryRenderedOptions;
use craft\fields\Categories;

/**
 * Renders a category field as rendered using a view mode
 */
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