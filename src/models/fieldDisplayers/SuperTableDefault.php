<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\SuperTableDefaultOptions;
use verbb\supertable\fields\SuperTableField;

/**
 * Renders a super table field
 */
class SuperTableDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'super-table-default';

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
        return \Craft::t('themes', 'Default');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [SuperTableField::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return SuperTableDefaultOptions::class;
    }
}