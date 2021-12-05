<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\CategoryLabelOptions;
use craft\fields\Categories;

/**
 * Renders a category field as a list
 */
class CategoryLabel extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'category_label';

    /**
     * @inheritDoc
     */
    public static function isDefault(string $fieldClass): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('app', 'Label');
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
        return CategoryLabelOptions::class;
    }
}