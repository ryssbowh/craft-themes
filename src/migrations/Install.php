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
        $this->createTable('{{%themes_blocks}}', [
        	'id' => $this->primaryKey(),
        	'region' => $this->string(255)->notNull(),
            'layout' => $this->integer(11)->unsigned()->notNull(),
        	'handle' => $this->string(255)->notNull(),
            'provider' => $this->string(255)->notNull(),
        	'order' => $this->integer(11)->unsigned()->notNull(),
        	'active' => $this->boolean()->defaultValue(true),
            'options' => $this->text(),
        	'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);

        $this->createTable('{{%themes_layouts}}', [
            'id' => $this->primaryKey(),
            'theme' => $this->string(255)->notNull(),
            'type' => $this->string(255)->notNull(),
            'element' => $this->string(255)->notNull(),
            'default_entry' => $this->boolean()->defaultValue(false),
            'default_category' => $this->boolean()->defaultValue(false),
            'default_route' => $this->boolean()->defaultValue(false),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);

        $this->createTable('{{%themes_view_modes}}', [
            'id' => $this->primaryKey(),
            'handle' => $this->string(255)->notNull(),
            'name' => $this->string(255)->notNull(),
            'layout' => $this->string(255)->notNull(),
            'theme' => $this->string(255)->notNull(),
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
        $this->dropTableIfExists('{{%themes_blocks}}');
        $this->dropTableIfExists('{{%themes_blocks_layouts}}');
        $this->dropTableIfExists('{{%themes_view_modes}}');
    }
}