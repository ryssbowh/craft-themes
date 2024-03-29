<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\ColourDefaultOptions;
use craft\fields\Color;

/**
 * Renders a colour field
 */
class ColourDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'colour-default';

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
        return [Color::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return ColourDefaultOptions::class;
    }
}