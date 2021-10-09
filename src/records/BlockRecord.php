<?php
namespace Ryssbowh\CraftThemes\records;

use craft\db\ActiveRecord;

class BlockRecord extends ActiveRecord
{
    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return '{{%themes_blocks}}';
    }
}