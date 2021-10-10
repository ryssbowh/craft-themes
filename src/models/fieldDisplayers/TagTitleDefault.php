<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use Ryssbowh\CraftThemes\models\fields\TagTitle;
use craft\base\Model;

/**
 * Renders a tag title field
 */
class TagTitleDefault extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'tag-title_default';

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

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
}