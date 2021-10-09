<?php

namespace Ryssbowh\CraftThemes\records;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
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