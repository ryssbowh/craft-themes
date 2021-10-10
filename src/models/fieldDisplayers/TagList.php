<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use craft\base\Model;
use craft\fields\Tags;

/**
 * Renders a tag field as a list
 */
class TagList extends FieldDisplayer
{
    /**
     * @inheritDoc
     */
    public static $handle = 'tag_list';

    /**
     * @inheritDoc
     */
    public static $isDefault = true;

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return \Craft::t('themes', 'List');
    }

    /**
     * @inheritDoc
     */
    public static function getFieldTarget(): String
    {
        return Tags::class;
    }
}