<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\TagTitleTitleOptions;
use Ryssbowh\CraftThemes\models\fields\TagTitle as TagTitleField;

/**
 * Renders a tag title field
 */
class TagTitle extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'tag-title';

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
        return \Craft::t('app', 'Title');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [TagTitleField::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return TagTitleTitleOptions::class;
    }
}