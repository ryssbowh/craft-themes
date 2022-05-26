<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\CraftFieldInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\models\Field;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\services\DisplayerCacheService;
use craft\base\Field as BaseField;
use craft\fieldlayoutelements\CustomField;
use craft\fields\BaseRelationField;
use craft\fields\Entries;

/**
 * Handles all Craft fields apart from Matrix and Table
 */
class CraftField extends Field implements CraftFieldInterface
{  
    /**
     * @inheritDoc
     */
    public function getTargetClass(): string
    {
        return get_class($this->craftField);
    }

    /**
     * @inheritDoc
     */
    public static function handleChanged(string $uid, array $data)
    {
        $field = Themes::$plugin->fields->getRecordByUid($uid);
        $craftField = \Craft::$app->fields->getFieldByUid($data['craft_field_id']);
        $data['craft_field_id'] = $craftField->id;
        $data['craft_field_class'] = get_class($craftField);
        $field->setAttributes($data, false);
        $field->save(false);
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(string $prefix = '', int $level = 0, array &$dependencies = []): array
    {
        if (!$this->displayer) {
            return [];
        }
        if ($level >= Themes::$plugin->settings->maxEagerLoadLevel) {
            \Craft::info("Maximum eager loaging level (" . Themes::$plugin->settings->maxEagerLoadLevel . ') reached', __METHOD__);
            return [];
        }
        if ($this->craftField instanceof BaseRelationField) {
            $dependencies[] = DisplayerCacheService::DISPLAYER_CACHE_TAG . '::' . $this->id;
            $with = $prefix  . $this->craftField->handle;
            return $this->displayer->eagerLoad([$with], $with . '.', $level + 1);
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        if ($parent = $this->parent) {
            //if the field has a parent it's inside a multi-field (matrix, super table etc)
            //we'll ask the parent for its name
            return $parent->getChildFieldName($this);
        }
        foreach ($this->layout->fieldLayout->tabs as $tab) {
            foreach ($tab->elements as $element) {
                if (get_class($element) == CustomField::class and $element->field->handle == $this->handle) {
                    return $element->label ?: $element->field->name;
                }
            }
        }
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getChildFieldName(FieldInterface $field): string
    {
        return $field->craftField->name;
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
    public function getDisplayName(): string
    {
        return $this->craftField::displayName();
    }

    /**
     * @inheritDoc
     */
    public function rebuild()
    {
        $this->craft_field_class = get_class($this->craftField);
        $this->type = Themes::$plugin->fields->getTypeForCraftField($this->craftField);
    }

    /**
     * @inheritDoc
     */
    public static function buildConfig(BaseField $craftField): array
    {
        $class = get_class($craftField);
        return [
            'type' => get_called_class()::getType(),
            'craft_field_id' => $craftField->id,
            'craft_field_class' => $class,
            'displayerHandle' => Themes::$plugin->fieldDisplayers->getDefaultHandle($class) ?? ''
        ];
    }
}