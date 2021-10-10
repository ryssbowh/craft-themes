<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\RedactorFullOptions;
use craft\base\Model;
use craft\redactor\Field;

/**
 * Renders a redactor field
 */
class RedactorFull extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'redactor_full';

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Full');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTarget(): String
    {
        return Field::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return RedactorFullOptions::class;
    }
}