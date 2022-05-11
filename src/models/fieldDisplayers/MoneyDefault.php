<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\MoneyDefaultOptions;
use craft\fields\Money;

/**
 * Renders a colour field
 */
class MoneyDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'money-default';

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
        return [Money::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return MoneyDefaultOptions::class;
    }
}