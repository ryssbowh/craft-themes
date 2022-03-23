<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\DimensionsDefaultOptions;
use Ryssbowh\CraftThemes\models\fields\Dimensions;
use craft\fields\Color;

/**
 * Renders a variant dimensions
 */
class DimensionsDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'dimensions-default';

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
            if (empty($value['width'] ?? null) and empty($value['length'] ?? null) and empty($value['length'] ?? null)) {
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
        return [Dimensions::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return DimensionsDefaultOptions::class;
    }
}