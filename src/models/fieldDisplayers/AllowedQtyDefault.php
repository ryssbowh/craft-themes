<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\AllowedQtyDefaultOptions;
use Ryssbowh\CraftThemes\models\fields\AllowedQty;
use craft\fields\Color;

/**
 * Renders a variant dimensions
 */
class AllowedQtyDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'allowed-qty-default';

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
        return \Craft::t('themes', 'Default');
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(&$value): bool
    {
        if (Themes::$plugin->settings->hideEmptyFields) {
            if (!is_numeric($value['min'] ?? null) and !is_numeric($value['max'] ?? null)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [AllowedQty::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return AllowedQtyDefaultOptions::class;
    }
}