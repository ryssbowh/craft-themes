<?php
namespace Ryssbowh\CraftThemes\services;

use Illuminate\Support\Collection;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\fields\Table;
use Ryssbowh\CraftThemes\records\TablePivotRecord;

class TablesService extends Service
{
    /**
     * @var Collection
     */
    private $_pivots;

    /**
     * Get all table pivots
     * 
     * @return Collection
     */
    public function getAllPivots(): Collection
    {
        if ($this->_pivots === null) {
            $records = TablePivotRecord::find()->orderBy(['order' => SORT_ASC])->all();
            $this->_pivots = collect();
            foreach ($records as $record) {
                $this->_pivots->push($record);
            }
        }
        return $this->_pivots;
    }

    /**
     * Get all pivots for a table field
     * 
     * @param  Table  $table
     * @return FieldInterface[]
     */
    public function getForTable(Table $table): array
    {
        return array_map(function ($pivot) {
            $field = Themes::$plugin->fields->getById($pivot->field_id);
            $field->name = $pivot->name;
            $field->handle = $pivot->handle;
            return $field;
        }, $this->allPivots
            ->where('table_id', $table->id)
            ->values()
            ->all()
        );
    }

    /**
     * Get a pivot record for a table and a field ids.
     * Creates new one if it doesn't exist
     * 
     * @param  int    $tableId
     * @param  int    $fieldId
     * @return TablePivotRecord
     */
    public function getTablePivotRecord(int $tableId, int $fieldId): TablePivotRecord
    {
        return $this->allPivots
            ->where('table_id', $tableId)
            ->firstWhere('field_id', $fieldId) 
            ?? new TablePivotRecord([
                'table_id' => $tableId,
                'field_id' => $fieldId
            ]);
    }

    /**
     * Get parent table field for a field
     * 
     * @param  int    $fieldId
     * @return ?Table
     */
    public function getTableForField(int $fieldId): ?Table
    {
        $pivot = $this->allPivots
            ->firstWhere('field_id', $fieldId);
        if ($pivot) {
            return $this->fieldsService()->getById($pivot->table_id);
        }
        return null;
    }
}