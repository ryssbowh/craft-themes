<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\FieldDisplayerException;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\models\DisplayItem;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\records\FieldRecord;
use Ryssbowh\CraftThemes\services\DisplayService;
use Ryssbowh\CraftThemes\services\FieldsService;
use craft\base\Element;
use craft\base\Field as BaseField;
use craft\helpers\StringHelper;

/**
 * Base class for all fields
 */
abstract class Field extends DisplayItem implements FieldInterface
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var int
     */
    public $craft_field_id;

    /**
     * @var string
     */
    public $craft_field_class;

    /**
     * @var string
     */
    protected $_displayerHandle;

    /**
     * @var BaseField
     */
    protected $_craftField;

    /**
     * @var FieldDisplayerInterface
     */
    protected $_displayer;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['displayerHandle', 'type'], 'string'],
            ['type', 'required'],
            ['options', 'safe'],
            ['type', 'in', 'range' => Themes::$plugin->fields->getValidTypes()]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getTargetClass(): string
    {
        return get_class($this);
    }

    /**
     * @inheritDoc
     */
    public function hasErrors($attribute = null)
    {
        if ($attribute !== null) {
            return parent::hasErrors($attribute);
        }
        $displayer = $this->displayer;
        if ($displayer and $displayer->hasErrors()) {
            return true;
        }
        return parent::hasErrors();
    }

    /**
     * @inheritDoc
     */
    public function getErrors($attribute = null)
    {
        $displayer = $this->displayer;
        if ($displayer and $attribute == 'displayer') {
            return $displayer->errors;
        }
        if ($attribute !== null) {
            return parent::getErrors($attribute);
        }
        $errors = parent::getErrors();
        if ($displayer and $errors2 = $displayer->errors) {
            $errors['displayer'] = $errors2;
        }
        return $errors;
    }

    /**
     * @inheritDoc
     */
    public function afterValidate()
    {
        if ($displayer = $this->displayer) {
            $displayer->validate();
        }
        parent::afterValidate();
    }

    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return DisplayService::TYPE_FIELD;
    }

    /**
     * @inheritDoc
     */
    public static function forField(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public static function create(?array $config = null): FieldInterface
    {
        $class = get_called_class();
        if ($config == null) {
            $config = $class::buildConfig(null);
        }
        $field = new $class;
        $attributes = $field->safeAttributes();
        $config = array_intersect_key($config, array_flip($attributes));
        $field->setAttributes($config);
        return $field;
    }

    /**
     * @inheritDoc
     */
    public static function save(FieldInterface $field): bool
    {
        $projectConfig = \Craft::$app->getProjectConfig();
        $configData = $field->getConfig();
        $uid = $field->uid ?? StringHelper::UUID();
        $configPath = FieldsService::CONFIG_KEY . '.' . $uid;
        $projectConfig->set($configPath, $configData);

        $record = Themes::$plugin->fields->getRecordByUid($uid);
        $field->setAttributes($record->getAttributes());
        return true;
    }

    /**
     * @inheritDoc
     */
    public static function handleChanged(string $uid, array $data)
    {
        $field = Themes::$plugin->fields->getRecordByUid($uid);
        $field->setAttributes($data, false);
        $field->save(false);
    }

    /**
     * @inheritDoc
     */
    public static function delete(FieldInterface $field): bool
    {
        \Craft::$app->getProjectConfig()->remove(FieldsService::CONFIG_KEY . '.' . $field->uid);
        return true;
    }

    /**
     * @inheritDoc
     */
    public static function handleDeleted(string $uid, array $data)
    {
        \Craft::$app->getDb()->createCommand()
            ->delete(FieldRecord::tableName(), ['uid' => $uid])
            ->execute();
        return true;
    }

    /**
     * @inheritDoc
     */
    public static function shouldExistOnLayout(LayoutInterface $layout): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function populateFromPost(array $data)
    {
        $attributes = $this->safeAttributes();
        $data = array_intersect_key($data, array_flip($attributes));
        $this->setAttributes($data);
    }

    /**
     * @inheritDoc
     */
    public function isVisible(): bool
    {
        if ($this->hidden or !$this->displayer) {
            return false;
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(): array
    {
        if ($this->displayer) {
            return $this->displayer->eagerLoad();
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return array_merge(parent::getConfig(), [
            'displayerHandle' => $this->displayerHandle,
            'options' => $this->options,
            'type' => $this->type
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getDisplayerHandle(): string
    {
        return $this->_displayerHandle;
    }

    /**
     * @inheritDoc
     */
    public function setDisplayerHandle(string $handle)
    {
        $this->_displayerHandle = $handle;
        $this->_displayer = null;
    }

    /**
     * @inheritDoc
     */
    public function getDisplayer(): ?FieldDisplayerInterface
    {
        if (!is_null($this->_displayer)) {
            return $this->_displayer;
        }
        if (!$this->displayerHandle) {
            return null;
        }
        try {
            $class = Themes::$plugin->fieldDisplayers->getClassByHandle($this->displayerHandle);
            $this->_displayer = new $class([
                'field' => $this
            ]);
        } catch (FieldDisplayerException $e) {
            //Field displayer is set but invalid (its handle has changed ?)          
        }
        return $this->_displayer;
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): array
    {
        if (!$this->displayer) {
            return [];
        }
        return $this->displayer->options->values;
    }

    /**
     * @inheritDoc
     */
    public function setOptions($options)
    {
        if ($this->displayer) {
            $this->displayer->options->setValues($options ?? []);
        }
    }

    /**
     * @inheritDoc
     */
    public function getAvailableDisplayers(): array
    {
        $displayers = Themes::$plugin->fieldDisplayers->getAvailable($this);
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
        return $this->getName();
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['availableDisplayers', 'name', 'handle', 'displayName', 'displayerHandle', 'options']);
    }

    /**
     * @inheritDoc
     */
    public function getFieldTemplates(): array
    {
        $type = $this->layout->type;
        $viewMode = $this->viewMode->handle;
        $key = $this->layout->templatingKey;
        $displayer = $this->displayer->handle;
        return [
            'fields/' . $type . '/' . $key . '/' . $viewMode . '/' . $displayer . '-' . $this->handle,
            'fields/' . $type . '/' . $key . '/' . $viewMode . '/' . $displayer,
            'fields/' . $type . '/' . $key . '/' . $displayer . '-' . $this->handle,
            'fields/' . $type . '/' . $key . '/' . $displayer,
            'fields/' . $type . '/' . $displayer . '-' . $this->handle,
            'fields/' . $type . '/' . $displayer,
            'fields/' . $displayer . '-' . $this->handle,
            'fields/' . $displayer
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFileTemplates(FileDisplayerInterface $displayer): array
    {
        $type = $this->layout->type;
        $viewMode = $this->viewMode->handle;
        $key = $this->layout->templatingKey;
        $displayer = $displayer->handle;
        return [
            'files/' . $type . '/' . $key . '/' . $viewMode . '/' . $displayer . '-' . $this->handle,
            'files/' . $type . '/' . $key . '/' . $viewMode . '/' . $displayer,
            'files/' . $type . '/' . $key . '/' . $displayer . '-' . $this->handle,
            'files/' . $type . '/' . $key . '/' . $displayer,
            'files/' . $type . '/' . $displayer . '-' . $this->handle,
            'files/' . $type . '/' . $displayer,
            'files/' . $displayer . '-' . $this->handle,
            'files/' . $displayer
        ];
    }

    /**
     * @inheritDoc
     */
    public function getRenderingValue()
    {
        return Themes::$plugin->view->renderingElement->{$this->handle};
    }

    /**
     * @inheritDoc
     */
    public function render($value = null): string
    {
        if ($value === null) {
            $value = $this->renderingValue;
        }
        return Themes::$plugin->view->renderField($this, $value);
    }

    /**
     * @inheritDoc
     */
    protected static function buildConfig($arg): array
    {
        $class = get_called_class();
        return [
            'type' => get_called_class()::getType(),
            'craft_field_id' => null,
            'craft_field_class' => null,
            'displayerHandle' => Themes::$plugin->fieldDisplayers->getDefaultHandle($class) ?? ''
        ];
    }
}