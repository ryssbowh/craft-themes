<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\StockDefaultOptions;
use Ryssbowh\CraftThemes\models\fields\Stock;

/**
 * Renders a number field
 */
class StockDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'stock-default';

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
    public function beforeRender(&$value): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [Stock::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return StockDefaultOptions::class;
    }
}