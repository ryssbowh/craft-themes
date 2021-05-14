<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\exceptions\DisplayException;
use Ryssbowh\CraftThemes\models\DisplayItem;
use Ryssbowh\CraftThemes\models\DisplayMatrixType;
use Ryssbowh\CraftThemes\models\DisplayTitle;
use Ryssbowh\CraftThemes\models\fields\CraftField;
use Ryssbowh\CraftThemes\models\fields\Field;
use Ryssbowh\CraftThemes\models\fields\Matrix;
use Ryssbowh\CraftThemes\models\fields\Title;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\records\FieldRecord;
use Ryssbowh\CraftThemes\records\MatrixPivotRecord;
use craft\base\Field as BaseField;
use craft\db\ActiveRecord;
use craft\fields\Matrix as CraftMatrix;
use craft\models\MatrixBlockType;

class FieldsService extends Service
{
    const TYPE_FIELD = 'field';
    const TYPE_MATRIX = 'matrix';
    const TYPE_TITLE = 'title';
    const TYPES = [self::TYPE_FIELD, self::TYPE_MATRIX, self::TYPE_TITLE];

    private $_fields;
    private $_matrixPivots;

    public function all()
    {
        if ($this->_fields === null) {
            $records = FieldRecord::find()->all();
            $this->_fields = collect();
            foreach ($records as $record) {
                $this->_fields->push($this->create($record));
            }
        }
        return $this->_fields;
    }

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

    public function getById(int $id): Field
    {
        return $this->all()->firstWhere('id', $id);
    }

    public function create($config): Field
    {
        if ($config instanceof ActiveRecord) {
            $config = $config->getAttributes();
        }
        if (!isset($config['type'])) {
            throw DisplayException::noType();
        }
        $typesData = null;
        switch ($config['type']) {
            case self::TYPE_TITLE:
                $class = Title::class;
                break;
            case self::TYPE_MATRIX:
                $typesData = $config['types'] ?? null;
                $class = Matrix::class;
                break;
            case self::TYPE_FIELD:
                $class = CraftField::class;
                break;
            default:
                throw DisplayException::unknownType($config['type']);
        }
        $field = new $class;
        $attributes = $field->safeAttributes();
        $config = array_intersect_key($config, array_flip($attributes));
        $field->setAttributes($config);
        if ($typesData) {
            $field->types = $this->buildMatrixTypes($typesData);
        }
        return $field;
    }

    public function createField(?BaseField $craftField): Field
    {
        $isMatrix = ($craftField ? get_class($craftField) == CraftMatrix::class : false);
        if (!$craftField) {
            return $this->createTitleField();
        } elseif ($isMatrix) {
            return $this->createMatrixField($craftField);
        }
        return $this->create($this->buildFieldConfig($craftField));
    }

    public function deleteField(Field $field)
    {
        \Craft::$app->getDb()->createCommand()
            ->delete(FieldRecord::tableName(), ['id' => $field->id])
            ->execute();
    }

    public function createTitleField(): Field
    {
        $displayer = $this->fieldDisplayersService()->getDefault(Title::class);
        return $this->create([
            'type' => self::TYPE_TITLE,
            'displayerHandle' => $displayer ? $displayer->handle : '',
            'options' => $displayer ? $displayer->getOptions()->toArray() : [],
            'hidden' => ($displayer == null)
        ]);
    }

    public function createMatrixField(CraftMatrix $craftField): Field
    {
        $types = [];
        $_this = $this;
        foreach ($craftField->getBlockTypes() as $type) {
            $types[] = [
                'type' => $type,
                'fields' => array_map(function ($field) use ($_this) {
                    return $_this->buildFieldConfig($field);
                }, $type->getFields())
            ];
        }
        $displayer = $this->fieldDisplayersService()->getDefault(get_class($craftField));
        $config = $this->buildFieldConfig($craftField, self::TYPE_MATRIX);
        $config['types'] = $types;
        return $this->create($config);
    }

    public function getForDisplay(int $id): Field
    {
        return $this->all()->firstWhere('display_id', $id);
    }

    public function getForMatrixType(MatrixBlockType $type, Matrix $matrix): array
    {
        $_this = $this;
        return array_map(function ($pivot) use ($_this) {
            return $_this->getById($pivot->field_id);
        }, $this->getMatrixPivotRecords($type->id, $matrix->id));
    } 

    public function save(array $data, DisplayRecord $display): bool
    {
        $data['display_id'] = $display->id;
        $method = 'save' . ucfirst($data['type']);
        $res = $this->$method($data);
        $this->_fields = null;
        $this->_matrixPivots = null;
        return $res;
    }

    protected function buildFieldConfig(BaseField $craftField, string $type = self::TYPE_FIELD): array
    {
        $displayer = $this->fieldDisplayersService()->getDefault(get_class($craftField));
        return [
            'type' => $type,
            'craft_field_id' => $craftField->id,
            'craft_field_class' => get_class($craftField),
            'displayerHandle' => $displayer ? $displayer->handle : '',
            'options' => $displayer ? $displayer->getOptions()->toArray() : [],
            'hidden' => ($displayer == null)
        ];
    }

    protected function buildMatrixTypes(array $data): array
    {
        $types = [];
        foreach ($data as $typeData) {
            $type_id = $typeData['type_id'] ?? $typeData['type']['id'];
            $type = \Craft::$app->matrix->getBlockTypeById($type_id);
            $fields = [];
            foreach ($typeData['fields'] as $fieldData) {
                $fields[] = $this->create($fieldData);
            }
            $types[] = new DisplayMatrixType([
                'type' => $type,
                'fields' => $fields
            ]);
        }
        return $types;
    }

    protected function getRecordByUid(string $uid): FieldRecord
    {
        return FieldRecord::findOne(['uid' => $uid]) ?? new FieldRecord;
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
        throw DisplayException::noMatrixType($uid);
    }

    protected function saveField(array $data): bool
    {
        $field = $this->getRecordByUid($data['uid']);
        $craftField = \Craft::$app->fields->getFieldByUid($data['craft_field_id']);
        $data['craft_field_id'] = $craftField->id;
        $data['craft_field_class'] = get_class($craftField);
        $field->setAttributes($data, false);
        return $field->save(false);
    }

    protected function saveTitle(array $data): bool
    {
        $field = $this->getRecordByUid($data['uid']);
        $isNew = $field->isNewRecord;
        $field->setAttributes($data, false);
        $res = $field->save(false);
        return $res;
    }

    protected function saveMatrix(array $data): bool
    {
        $matrix = $this->getRecordByUid($data['uid']);
        $types = $data['types'] ?? [];
        unset($data['types']);
        $field = \Craft::$app->fields->getFieldByUid($data['craft_field_id']);
        $data['craft_field_id'] = $field->id;
        $data['craft_field_class'] = get_class($field);
        $matrix->setAttributes($data, false);
        $res = $matrix->save(false);
        foreach ($types as $typeData) {
            $fields = $typeData['fields'] ?? [];
            unset($typeData['fields']);
            $type = $this->getMatrixBlockTypeByUid($typeData['type_uid']);
            foreach ($fields as $order => $fieldData) {
                $field = $this->getRecordByUid($fieldData['uid']);
                $fieldData['craft_field_id'] = \Craft::$app->fields->getFieldByUid($fieldData['craft_field_id'])->id;
                $field->setAttributes($fieldData, false);
                $field->save(false);
                $pivot = $this->getMatrixPivotRecord($type->id, $matrix->id, $field->id);
                $pivot->field_id = $field->id;
                $pivot->parent_id = $matrix->id;
                $pivot->matrix_type_id = $type->id;
                $pivot->order = $order;
                if (!$pivot->id) {
                    $this->_matrixPivots->push($pivot);
                }
                $pivot->save(false);
            }
        }
        return $res;
    }
}