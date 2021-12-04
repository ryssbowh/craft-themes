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
    public static function isDefault(string $fieldClass): bool
    {
        return true;
    }

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
    public static function getFieldTargets(): array
    {
        return [PlainText::class];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return PlainTextFullOptions::class;
    }
}