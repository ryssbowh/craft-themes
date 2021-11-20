<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\PlainTextFullOptions;
use craft\base\Model;
use craft\fields\PlainText;

/**
 * Renders a plain text field
 */
class PlainTextFull extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'plain_text_full';

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
        return PlainText::class;
    }
}