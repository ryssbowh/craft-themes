<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\WeightDefaultOptions;
use Ryssbowh\CraftThemes\models\fields\Weight;
use craft\fields\Color;

/**
 * Renders a variant dimensions
 */
class WeightDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'weight-default';

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
        return [Weight::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return WeightDefaultOptions::class;
    }
}