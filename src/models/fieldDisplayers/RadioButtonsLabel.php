<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\RadioButtonsLabelOptions;
use craft\fields\RadioButtons;

/**
 * Renders a radio butons field
 */
class RadioButtonsLabel extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'radio_buttons_label';

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
        return [RadioButtons::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return RadioButtonsLabelOptions::class;
    }
}