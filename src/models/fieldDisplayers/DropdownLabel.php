<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\DropdownLabelOptions;
use craft\fields\Dropdown;

/**
 * Renders a dropdown field
 */
class DropdownLabel extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'dropdown-label';

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
        return [Dropdown::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return DropdownLabelOptions::class;
    }
}