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
    public function allPivots(): Collection
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

    public function getForTable(Table $table): array
    {
        return array_map(function ($pivot) {
            $field = Themes::$plugin->fields->getById($pivot->field_id);
            $field->name = $pivot->name;
            $field->handle = $pivot->handle;
            return $field;
        }, $this->allPivots()
            ->where('table_id', $table->id)
            ->values()
            ->all()
        );
    }

    public function getTablePivotRecord(int $tableId, int $fieldId): TablePivotRecord
    {
        return $this->allPivots()
            ->where('table_id', $tableId)
            ->firstWhere('field_id', $fieldId) 
            ?? new TablePivotRecord([
                'table_id' => $tableId,
                'field_id' => $fieldId
            ]);
    }

    public function getTableForField(int $fieldId): ?Table
    {
        $pivot = $this->allPivots()
            ->firstWhere('field_id', $fieldId);
        if ($pivot) {
            return $this->fieldsService()->getById($pivot->table_id);
        }
        return null;
    }
}