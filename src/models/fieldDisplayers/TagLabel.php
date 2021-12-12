<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\TagLabelOptions;
use craft\fields\Tags;

/**
 * Renders a tag field as a list
 */
class TagLabel extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'tag_label';

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
        return \Craft::t('app', 'Label');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTargets(): array
    {
        return [Tags::class];
    }

    /**
     * @inheritDoc
     */
    protected function getOptionsModel(): string
    {
        return TagLabelOptions::class;
    }
}