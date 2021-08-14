<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\TimeDefaultOptions;
use craft\base\Model;
use craft\fields\Time;

class TimeDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'time_default';

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
        return Time::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return TimeDefaultOptions::class;
    }
}