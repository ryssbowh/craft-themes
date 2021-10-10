<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\RedactorTruncatedOptions;
use craft\base\Model;
use craft\redactor\Field;

/**
 * Renders a redactor field as truncated
 */
class RedactorTruncated extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'redactor_truncated';

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'Truncated');
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
        return RedactorTruncatedOptions::class;
    }
}