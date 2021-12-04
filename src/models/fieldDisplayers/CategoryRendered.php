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
        $elems = explode(':', $this->field->craftField->source);
        $group = \Craft::$app->categories->getGroupByUid($elems[1]);
        if ($group) {
            return $group->getLayout();
        }
        return null;
    }
}