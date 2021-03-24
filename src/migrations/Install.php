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
            'layout_id' => $this->integer(11)->notNull(),
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
            'hasBlocks' => $this->boolean()->defaultValue(false),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);

        $this->createTable('{{%themes_view_modes}}', [
            'id' => $this->primaryKey(),
            'handle' => $this->string(255)->notNull(),
            'name' => $this->string(255)->notNull(),
            'layout_id' => $this->integer(11)->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);

        $this->createTable('{{%themes_displays}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(255)->notNull(),
            'order' => $this->integer(11),
            'viewMode_id' => $this->integer(11)->notNull(),
            'labelHidden' => $this->boolean()->defaultValue(false),
            'labelVisuallyHidden' => $this->boolean()->defaultValue(false),
            'hidden' => $this->boolean()->defaultValue(false),
            'visuallyHidden' => $this->boolean()->defaultValue(false),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);

        $this->createTable('{{%themes_groups}}', [
            'id' => $this->primaryKey(),
            'display_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
            'handle' => $this->string(255)->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);

        $this->createTable('{{%themes_matrix}}', [
            'id' => $this->primaryKey(),
            'display_id' => $this->integer(11)->notNull(),
            'fieldUid' => $this->string(45)->notNull(),
            'displayerHandle' => $this->string(255)->notNull(),
            'options' => $this->text(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);

        $this->createTable('{{%themes_fields}}', [
            'id' => $this->primaryKey(),
            'display_id' => $this->integer(11),
            'fieldUid' => $this->string(45)->notNull(),
            'displayerHandle' => $this->string(255)->notNull(),
            'options' => $this->text(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);

        $this->createTable('{{%themes_pivot_group_field}}', [
            'id' => $this->primaryKey(),
            'group_id' => $this->integer(11)->notNull(),
            'field_id' => $this->integer(11)->notNull(),
            'order' => $this->integer(11)
        ]);

        $this->createTable('{{%themes_pivot_matrix_field}}', [
            'id' => $this->primaryKey(),
            'matrix_id' => $this->integer(11)->notNull(),
            'field_id' => $this->integer(11)->notNull(),
            'order' => $this->integer(11)
        ]);

        $this->addForeignKey('themes_display_view_mode', '{{%themes_displays}}', ['viewMode_id'], '{{%themes_view_modes}}', ['id'], 'CASCADE', null);
        $this->addForeignKey('themes_view_mode_layout', '{{%themes_view_modes}}', ['layout_id'], '{{%themes_layouts}}', ['id'], 'CASCADE', null);
        $this->addForeignKey('themes_blocks_layout', '{{%themes_blocks}}', ['layout_id'], '{{%themes_layouts}}', ['id'], 'CASCADE', null);
        $this->addForeignKey('themes_fields_display', '{{%themes_fields}}', ['display_id'], '{{%themes_displays}}', ['id'], 'CASCADE', null);

        $this->addForeignKey('themes_display_pivot_group_field_field', '{{%themes_pivot_group_field}}', ['field_id'], '{{%themes_fields}}', ['id'], 'CASCADE', null);
        $this->addForeignKey('themes_display_pivot_group_field_group', '{{%themes_pivot_group_field}}', ['group_id'], '{{%themes_groups}}', ['id'], 'CASCADE', null);
        $this->addForeignKey('themes_pivot_matrix_field_field', '{{%themes_pivot_matrix_field}}', ['field_id'], '{{%themes_fields}}', ['id'], 'CASCADE', null);
        $this->addForeignKey('themes_pivot_matrix_field_matrix', '{{%themes_pivot_matrix_field}}', ['matrix_id'], '{{%themes_matrix}}', ['id'], 'CASCADE', null);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('themes_display_view_mode', '{{%themes_displays}}');
        $this->dropForeignKey('themes_view_mode_layout', '{{%themes_view_modes}}');
        $this->dropForeignKey('themes_blocks_layout', '{{%themes_blocks}}');
        $this->dropForeignKey('themes_fields_display', '{{%themes_fields}}');
        $this->dropForeignKey('themes_display_pivot_group_field_field', '{{%themes_pivot_group_field}}');
        $this->dropForeignKey('themes_display_pivot_group_field_group', '{{%themes_pivot_group_field}}');
        $this->dropForeignKey('themes_pivot_matrix_field_field', '{{%themes_pivot_matrix_field}}');
        $this->dropForeignKey('themes_pivot_matrix_field_matrix', '{{%themes_pivot_matrix_field}}');

        $this->dropTableIfExists('{{%themes_fields}}');
        $this->dropTableIfExists('{{%themes_groups}}');
        $this->dropTableIfExists('{{%themes_matrix}}');
        $this->dropTableIfExists('{{%themes_blocks}}');
        $this->dropTableIfExists('{{%themes_displays}}');
        $this->dropTableIfExists('{{%themes_view_modes}}');
        $this->dropTableIfExists('{{%themes_layouts}}');
        $this->dropTableIfExists('{{%themes_pivot_group_field}}');
        $this->dropTableIfExists('{{%themes_pivot_matrix_field}}');
    }
}