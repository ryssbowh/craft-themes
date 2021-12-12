<?php
namespace Ryssbowh\CraftThemes\services;

use Illuminate\Support\Collection;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\FieldException;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\models\fields\Matrix;
use Ryssbowh\CraftThemes\records\MatrixPivotRecord;
use craft\models\MatrixBlockType;

class MatrixService extends Service
{
    /**
     * @var Collection
     */
    private $_matrixPivots;

    /**
     * Get all matrix pivots
     * 
     * @return Collection
     */
    public function allMatrixPivots(): Collection
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

    /**
     * Get all fields for a matrix type
     * 
     * @param  MatrixBlockType $type
     * @param  Matrix          $matrix
     * @return FieldInterface[]
     */
    public function getForMatrixType(MatrixBlockType $type, Matrix $matrix): array
    {
        return array_map(function ($pivot) {
            return Themes::$plugin->fields->getById($pivot->field_id);
        }, $this->getMatrixPivotRecords($type, $matrix));
    }

    /**
     * Get a matrix block type by uid
     * 
     * @param  string $uid
     * @return MatrixBlockType
     * @throws FieldException
     */
    public function getMatrixBlockTypeByUid(string $uid)
    {
        foreach (\Craft::$app->matrix->getAllBlockTypes() as $type) {
            if ($type->uid == $uid) {
                return $type;
            }
        }
        throw FieldException::noMatrixType($uid);
    }

    /**
     * Get the Matrix field for a field
     * 
     * @param  int    $fieldId
     * @return ?Matrix
     */
    public function getMatrixForField(int $fieldId): ?Matrix
    {
        $pivot = $this->allMatrixPivots()
            ->firstWhere('field_id', $fieldId);
        if ($pivot) {
            return $this->fieldsService()->getById($pivot->parent_id);
        }
        return null;
    }

    /**
     * Get a pivot record, or creates a new one
     * 
     * @param  int    $typeId
     * @param  int    $matrixId
     * @param  int    $fieldId
     * @return MatrixPivotRecord
     */
    public function getMatrixPivotRecord(int $typeId, int $matrixId, int $fieldId): MatrixPivotRecord
    {
        return $this->allMatrixPivots()
            ->where('matrix_type_id', $typeId)
            ->where('parent_id', $matrixId)
            ->firstWhere('field_id', $fieldId) 
            ?? new MatrixPivotRecord([
                'matrix_type_id' => $typeId,
                'parent_id' => $matrixId,
                'field_id' => $fieldId
            ]);
    }

    /**
     * Get all pivots for a block type and a matrix id
     * 
     * @param  MatrixBlockType $type
     * @param  Matrix          $matrix
     * @return MatrixPivotRecord[]
     */
    protected function getMatrixPivotRecords(MatrixBlockType $type, Matrix $matrix): array
    {
        return $this->allMatrixPivots()
            ->where('matrix_type_id', $type->id)
            ->where('parent_id', $matrix->id)
            ->values()->all();
    }
}