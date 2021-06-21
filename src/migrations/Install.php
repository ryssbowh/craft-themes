<?php

namespace Ryssbowh\CraftThemes\migrations;

use craft\db\Migration;
use craft\db\Table;

class Install extends Migration
{
	/**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableSchema = \Yii::$app->db->schema->getTableSchema('{{%themes_blocks}}');
        if ($tableSchema) {
            return;
        }
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
            'order' => $this->integer(11),
            'type' => $this->string(255)->notNull(),
            'viewMode_id' => $this->integer(11)->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);

        $this->createTable('{{%themes_groups}}', [
            'id' => $this->primaryKey(),
            'display_id' => $this->integer(11),
            'name' => $this->string(255)->notNull(),
            'handle' => $this->string(255)->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);

        $this->createTable('{{%themes_fields}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(255)->notNull(),
            'display_id' => $this->integer(11),
            'craft_field_id' => $this->integer(11),
            'craft_field_class' => $this->string(255),
            'displayerHandle' => $this->string(255)->notNull(),
            'options' => $this->text(),
            'labelHidden' => $this->boolean()->defaultValue(false),
            'labelVisuallyHidden' => $this->boolean()->defaultValue(false),
            'hidden' => $this->boolean()->defaultValue(false),
            'visuallyHidden' => $this->boolean()->defaultValue(false),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);

        $this->createTable('{{%themes_pivot_group}}', [
            'id' => $this->primaryKey(),
            'group_id' => $this->integer(11)->notNull(),
            'field_id' => $this->integer(11)->notNull(),
            'order' => $this->integer(11),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);

        $this->createTable('{{%themes_pivot_matrix}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(11)->notNull(),
            'field_id' => $this->integer(11)->notNull(),
            'matrix_type_id' => $this->integer(11)->notNull(),
            'order' => $this->integer(11),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);

        $this->createTable('{{%themes_pivot_table}}', [
            'id' => $this->primaryKey(),
            'table_id' => $this->integer(11)->notNull(),
            'field_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
            'handle' => $this->string(255)->notNull(),
            'order' => $this->integer(11),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);

        $this->addForeignKey('themes_display_view_mode', '{{%themes_displays}}', ['viewMode_id'], '{{%themes_view_modes}}', ['id'], 'CASCADE', null);

        $this->addForeignKey('themes_view_mode_layout', '{{%themes_view_modes}}', ['layout_id'], '{{%themes_layouts}}', ['id'], 'CASCADE', null);

        $this->addForeignKey('themes_blocks_layout', '{{%themes_blocks}}', ['layout_id'], '{{%themes_layouts}}', ['id'], 'CASCADE', null);

        $this->addForeignKey('themes_fields_display', '{{%themes_fields}}', ['display_id'], '{{%themes_displays}}', ['id'], 'CASCADE', null);

        $this->addForeignKey('themes_pivot_group_field', '{{%themes_pivot_group}}', ['field_id'], '{{%themes_fields}}', ['id'], 'CASCADE', null);
        $this->addForeignKey('themes_pivot_group_group', '{{%themes_pivot_group}}', ['group_id'], '{{%themes_groups}}', ['id'], 'CASCADE', null);

        $this->addForeignKey('themes_pivot_matrix_field', '{{%themes_pivot_matrix}}', ['field_id'], '{{%themes_fields}}', ['id'], 'CASCADE', null);
        $this->addForeignKey('themes_pivot_matrix_parent', '{{%themes_pivot_matrix}}', ['parent_id'], '{{%themes_fields}}', ['id'], 'CASCADE', null);

        $this->addForeignKey('themes_pivot_table_field', '{{%themes_pivot_table}}', ['field_id'], '{{%themes_fields}}', ['id'], 'CASCADE', null);
        $this->addForeignKey('themes_pivot_table_parent', '{{%themes_pivot_table}}', ['table_id'], '{{%themes_fields}}', ['id'], 'CASCADE', null);
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
        $this->dropForeignKey('themes_pivot_group_field', '{{%themes_pivot_group}}');
        $this->dropForeignKey('themes_pivot_group_group', '{{%themes_pivot_group}}');
        $this->dropForeignKey('themes_pivot_matrix_field', '{{%themes_pivot_matrix}}');
        $this->dropForeignKey('themes_pivot_matrix_parent', '{{%themes_pivot_matrix}}');
        $this->dropForeignKey('themes_pivot_table_field', '{{%themes_pivot_table}}');
        $this->dropForeignKey('themes_pivot_table_parent', '{{%themes_pivot_table}}');

        $this->dropTableIfExists('{{%themes_fields}}');
        $this->dropTableIfExists('{{%themes_groups}}');
        $this->dropTableIfExists('{{%themes_blocks}}');
        $this->dropTableIfExists('{{%themes_displays}}');
        $this->dropTableIfExists('{{%themes_view_modes}}');
        $this->dropTableIfExists('{{%themes_layouts}}');
        $this->dropTableIfExists('{{%themes_pivot_group}}');
        $this->dropTableIfExists('{{%themes_pivot_matrix}}');
        $this->dropTableIfExists('{{%themes_pivot_table}}');
    }
}