<?php

namespace Ryssbowh\CraftThemes\migrations;

use craft\db\Migration;

class Install extends Migration
{
	/**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%theme_block_layouts}}', [
        	'id' => $this->primaryKey(),
        	'region' => $this->string(255)->notNull(),
            'theme' => $this->string(255)->notNull(),
        	'blocHandle' => $this->string(255)->notNull(),
            'blockProvider' => $this->string(255)->notNull(),
        	'order' => $this->integer(11)->notNull(),
        	'active' => $this->boolean()->defaultValue(true),
            'options' => $this->text(),
        	'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTableIfExists('{{%theme_block_layouts}}');
    }
}