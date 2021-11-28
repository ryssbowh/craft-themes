<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\RadioButtonsLabelOptions;
use craft\base\Model;
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
    public static $isDefault = true;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Label');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTarget(): String
    {
        return RadioButtons::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return RadioButtonsLabelOptions::class;
    }
}