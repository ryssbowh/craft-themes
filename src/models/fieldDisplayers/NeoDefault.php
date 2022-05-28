<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\NeoDefaultOptions;
use benf\neo\Field;

/**
 * Renders a Neo field
 *
 * @since 3.2.0
 */
class NeoDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'neo-default';

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
        return [Field::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return NeoDefaultOptions::class;
    }
}