<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\NumberDefaultOptions;
use craft\base\Model;
use craft\fields\Number;

class NumberDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'number_default';

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

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
    public static function getFieldTarget(): String
    {
        return Number::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return NumberDefaultOptions::class;
    }
}