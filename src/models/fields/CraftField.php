<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\CraftFieldInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use craft\base\Field as BaseField;

class CraftField extends Field implements CraftFieldInterface
{
    /**
     * @inheritDoc
     */
    public static function save(array $data): bool
    {
        $field = Themes::$plugin->fields->getRecordByUid($data['uid']);
        $craftField = \Craft::$app->fields->getFieldByUid($data['craft_field_id']);
        $data['craft_field_id'] = $craftField->id;
        $data['craft_field_class'] = get_class($craftField);
        $field->setAttributes($data, false);
        return $field->save(false);
    }
    
    /**
     * @inheritDoc
     */
    public static function createFromField(BaseField $craftField): FieldInterface
    {
        return static::create(static::buildConfig($craftField));
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['craft_field_id'], 'integer'],
            [['craft_field_class'], 'string']
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return array_merge(parent::getConfig(), [
            'craft_field_id' => $this->craftField->uid,
            'craft_field_class' => get_class($this->craftField)
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getCraftField(): BaseField
    {
        if ($this->_craftField === null) {
            $this->_craftField = \Craft::$app->fields->getFieldById($this->craft_field_id);
        }
        return $this->_craftField;
    }

    /**
     * Get view mode associated to this field
     * 
     * @return ViewMode
     */
    public function getViewMode(): ViewMode
    {
        return $this->display->viewMode;
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return $this->craftField->handle;
    }

    /**
     * @inheritDoc
     */
    public function getAvailableDisplayers(): array
    {
        return Themes::$plugin->fieldDisplayers->getForField(get_class($this->craftField), $this);
    }

    /**
     * @inheritDoc
     */
    public function getDisplayName(): string
    {
        return $this->craftField::displayName();
    }

    /**
     * @inheritDoc
     */
    protected static function buildConfig($craftField): array
    {
        $class = get_class($craftField);
        return [
            'type' => get_called_class()::getType(),
            'craft_field_id' => $craftField->id,
            'craft_field_class' => get_class($craftField),
            'displayerHandle' => Themes::$plugin->fieldDisplayers->getDefaultHandle($class) ?? ''
        ];
    }
}