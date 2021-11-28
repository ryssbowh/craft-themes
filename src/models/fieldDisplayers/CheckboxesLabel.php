<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\CheckboxesLabelOptions;
use craft\base\Model;
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
        return Checkboxes::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return CheckboxesLabelOptions::class;
    }
}