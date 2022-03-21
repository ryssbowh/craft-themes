<?php

namespace Ryssbowh\CraftThemes\migrations;

use Craft;
use Ryssbowh\CraftThemes\records\ParentPivotRecord;
use craft\db\Migration;

/**
 * m220303_071106_AddParentTable migration.
 */
class m220303_071106_AddParentTable extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%themes_pivot_parents}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer(11)->notNull(),
            'field_id' => $this->integer(11)->notNull(),
            'order' => $this->integer(11),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);
        $this->addForeignKey('themes_pivot_parents_field', '{{%themes_pivot_parents}}', ['field_id'], '{{%themes_fields}}', ['id'], 'CASCADE', null);
        $this->addForeignKey('themes_pivot_parents_parent', '{{%themes_pivot_parents}}', ['parent_id'], '{{%themes_fields}}', ['id'], 'CASCADE', null);

        //Change all matrix pivots into parent pivots
        $matrixPivots = (new \craft\db\Query())->select('*')->from('themes_pivot_matrix')->all();
        foreach ($matrixPivots as $pivot) {
            $parentPivot = new ParentPivotRecord([
                'uid' => $pivot->uid,
                'parent_id' => $pivot->parent_id,
                'field_id' => $pivot->field_id,
                'order' => $pivot->order,
                'dateCreated' => $pivot->dateCreated,
                'dateUpdated' => $pivot->dateUpdated
            ]);
            $parentPivot->save(false);
        }

        //Change all 'matrix-field' types into 'field'
        $matrixFields = (new \craft\db\Query())->select('*')->from('themes_fields')->where(['type' => 'matrix-field'])->all();
        foreach ($matrixFields as $field) {
            $field->type = 'field';
            $field->save(false);
        }

        $this->dropForeignKey('themes_pivot_matrix_field', '{{%themes_pivot_matrix}}');
        $this->dropForeignKey('themes_pivot_matrix_parent', '{{%themes_pivot_matrix}}');
        $this->dropTableIfExists('{{%themes_pivot_matrix}}');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        return false;
    }
}
