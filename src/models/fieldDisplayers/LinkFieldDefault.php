<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\LinkFieldDefaultOptions;
use typedlinkfield\fields\LinkField;

/**
 * Renders one link field
 */
class LinkFieldDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'link-field';

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
        return \Craft::t('themes', 'Link');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [LinkField::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return LinkFieldDefaultOptions::class;
    }
}