<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

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
    public function getOptionsModel(): string
    {
        return NumberDefaultOptions::class;
    }
}