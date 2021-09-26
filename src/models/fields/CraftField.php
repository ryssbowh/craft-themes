<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\CraftFieldInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use craft\base\Field as BaseField;
use craft\fieldlayoutelements\CustomField;

class CraftField extends Field implements CraftFieldInterface
{
    /**
     * @inheritDoc
     */
    public static function save(string $uid, array $data): bool
    {
        $field = Themes::$plugin->fields->getRecordByUid($uid);
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
    public function onCraftFieldChanged(BaseField $field): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        foreach ($this->layout->fieldLayout->tabs as $tab) {
            foreach ($tab->elements as $element) {
                if (get_class($element) == CustomField::class and $element->field->handle == $this->handle) {
                    return $element->label ?? $element->field->name;
                }
            }
        }
        return '';
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
     * @return ViewModeInterface
     */
    public function getViewMode(): ViewModeInterface
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
        $displayers = Themes::$plugin->fieldDisplayers->getForField(get_class($this->craftField));
        $_this = $this;
        array_walk($displayers, function ($displayer) use ($_this) {
            $displayer->field = $_this;
        });
        return $displayers;
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