<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\FieldDisplayerException;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
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
use Twig\Markup;
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
     * @var FieldInterface|null
     */
    protected $_parent;

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
            ['craft_field_id', 'integer'],
            [['parent', 'options'], 'safe'],
            ['type', 'in', 'range' => Themes::$plugin->fields->getValidTypes()]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getDisplay(): DisplayInterface
    {
        if ($parent = $this->parent) {
            return $parent->getDisplay();
        }
        return parent::getDisplay();
    }

    /**
     * @inheritDoc
     */
    public function getParent(): ?FieldInterface
    {
        if ($this->_parent === null and $this->id) {
            $this->_parent = Themes::$plugin->fields->getParent($this);
        }
        return $this->_parent;
    }

    /**
     * @inheritDoc
     */
    public function setParent(?FieldInterface $field)
    {
        $this->_parent = $field;
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(string $prefix = '', int $level = 0, array &$dependencies = []): array
    {
        return [];
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
    public static function create(array $config): FieldInterface
    {
        $class = get_called_class();
        $config['displayerHandle'] = $config['displayerHandle'] ?? Themes::$plugin->fieldDisplayers->getDefaultHandle($class) ?? '';
        $field = new $class;
        $field->populateFromData($config);
        return $field;
    }

    /**
     * @inheritDoc
     */
    public function populateFromData(array $data)
    {
        $attributes = $this->safeAttributes();
        $data = array_intersect_key($data, array_flip($attributes));
        $this->setAttributes($data);
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
        if (!isset($data['options'])) {
            $data['options'] = [];
        }
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
    public function getConfig(): array
    {
        $config = array_merge(parent::getConfig(), [
            'displayerHandle' => $this->displayerHandle,
            'options' => $this->options,
            'type' => $this->type
        ]);
        if ($parent = $this->parent) {
            unset($config['display_id']);
        }
        return $config;
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
            \Craft::$app->errorHandler->logException($e);
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
            $this->displayer->options->replaceValues($options ?? []);
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
        return $this->layout->getFieldTemplates($this);
    }

    /**
     * @inheritDoc
     */
    public function getFileTemplates(FileDisplayerInterface $displayer): array
    {
        return $this->layout->getFileTemplates($this, $displayer);
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
    public function render($value = null): Markup
    {
        if ($value === null) {
            $value = $this->renderingValue;
        }
        return Themes::$plugin->view->renderField($this, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCanBeCached(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function rebuild(): bool
    {
        return false;
    }
}