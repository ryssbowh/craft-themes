<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\CheckboxesLabelOptions;
use craft\fields\Checkboxes;

/**
 * Renders a checkboxes field
 */
class CheckboxesLabel extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'checkboxes_label';

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
        return [Checkboxes::class];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return CheckboxesLabelOptions::class;
    }
}