<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\exceptions\FieldException;
use Ryssbowh\CraftThemes\records\MatrixPivotRecord;
use craft\models\MatrixBlockType;

class MatrixService extends Service
{
    private $_matrixPivots;

    public function allMatrixPivots()
    {
        if ($this->_matrixPivots === null) {
            $records = MatrixPivotRecord::find()->orderBy(['order' => SORT_ASC])->all();
            $this->_matrixPivots = collect();
            foreach ($records as $record) {
                $this->_matrixPivots->push($record);
            }
        }
        return $this->_matrixPivots;
    }

    public function getForMatrixType(MatrixBlockType $type, Matrix $matrix): array
    {
        $_this = $this;
        return array_map(function ($pivot) use ($_this) {
            return $_this->getById($pivot->field_id);
        }, $this->getMatrixPivotRecords($type->id, $matrix->id));
    }

    protected function getMatrixPivotRecord(int $typeId, int $matrixId, int $fieldId)
    {
        return $this->allMatrixPivots()
            ->where('matrix_type_id', $typeId)
            ->where('parent_id', $matrixId)
            ->firstWhere('field_id', $fieldId) ?? new MatrixPivotRecord;
    }

    protected function getMatrixPivotRecords(int $typeId, int $matrixId): array
    {
        return $this->allMatrixPivots()
            ->where('matrix_type_id', $typeId)
            ->where('parent_id', $matrixId)
            ->values()->all();
    }

    protected function getMatrixBlockTypeByUid(string $uid)
    {
        foreach (\Craft::$app->matrix->getAllBlockTypes() as $type) {
            if ($type->uid == $uid) {
                return $type;
            }
        }
        throw FieldException::noMatrixType($uid);
    }
}