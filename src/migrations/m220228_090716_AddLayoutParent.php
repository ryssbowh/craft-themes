<?php

namespace Ryssbowh\CraftThemes\migrations;

use Craft;
use craft\db\Migration;

/**
 * m220228_090716_AddLayoutParent migration.
 */
class m220228_090716_AddLayoutParent extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%themes_layouts}}', 'parent_id', $this->integer(11)->null()->after('id'));
        $this->addForeignKey('themes_layouts_parent', '{{%themes_layouts}}', ['parent_id'], '{{%themes_layouts}}', ['id'], 'SET NULL');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('themes_layouts_parent', '{{%themes_layouts}}');
        $this->dropColumn('{{%themes_layouts}}', 'parent_id');
    }
}
