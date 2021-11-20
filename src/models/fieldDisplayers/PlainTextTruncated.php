<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\PlainTextTruncatedOptions;
use craft\base\Model;
use craft\fields\PlainText;

/**
 * Renders a plain text field as truncated
 */
class PlainTextTruncated extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'plain_text_truncated';

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
        return PlainText::class;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return PlainTextTruncatedOptions::class;
    }
}