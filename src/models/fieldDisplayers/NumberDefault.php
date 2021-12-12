<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\NumberDefaultOptions;
use craft\fields\Number;

/**
 * Renders a number field
 */
class NumberDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'number_default';

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
    public static function getFieldTargets(): array
    {
        return [Number::class];
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(&$value): bool
    {
        return !($value === null and Themes::$plugin->settings->hideEmptyFields);
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return NumberDefaultOptions::class;
    }
}