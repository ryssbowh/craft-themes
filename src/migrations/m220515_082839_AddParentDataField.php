<?php

namespace Ryssbowh\CraftThemes\migrations;

use Craft;
use craft\db\Migration;

/**
 * m220515_082839_AddParentDataField migration.
 */
class m220515_082839_AddParentDataField extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%themes_pivot_parents}}', 'data', $this->text()->after('field_id'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->removeColumn('{{%themes_pivot_parents}}', 'data');
    }
}
