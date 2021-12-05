<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\RedactorFullOptions;
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
        return [Field::class];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return RedactorFullOptions::class;
    }
}