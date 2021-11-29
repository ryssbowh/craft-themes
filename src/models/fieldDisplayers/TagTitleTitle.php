<?php
namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fieldDisplayerOptions\TagTitleTitleOptions;
use Ryssbowh\CraftThemes\models\fields\TagTitle;
use craft\base\Model;

/**
 * Renders a tag title field
 */
class TagTitleTitle extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'tag-title_title';

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

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
    public static function getFieldTarget(): String
    {
        return TagTitle::class;
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): string
    {
        return TagTitleTitleOptions::class;
    }
}