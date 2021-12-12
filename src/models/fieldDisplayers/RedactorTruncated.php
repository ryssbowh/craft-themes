<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\RedactorTruncatedOptions;
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
    public function getDescription(): string
    {
        return '<span class="with-icon warning"></span>' . \Craft::t('themes', 'Truncating content will always strip Html tags');
    }

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
    public static function getFieldTargets(): array
    {
        return [Field::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return RedactorTruncatedOptions::class;
    }
}