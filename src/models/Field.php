<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\DisplayItem;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\records\FieldRecord;
use Ryssbowh\CraftThemes\services\DisplayService;
use Ryssbowh\CraftThemes\services\FieldsService;
use craft\base\Element;
use craft\base\Field as BaseField;
use craft\fieldlayoutelements\CustomField;
use craft\fields\Matrix as CraftMatrix;
use craft\helpers\StringHelper;

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
        $config['uid'] = $config['uid'] ?? StringHelper::UUID();
        $field = new $class;
        $attributes = $field->safeAttributes();
        $config = array_intersect_key($config, array_flip($attributes));
        $field->setAttributes($config);
        return $field;
    }

    /**
     * @inheritDoc
     */
    public static function save(array $data): bool
    {
        $field = Themes::$plugin->fields->getRecordByUid($data['uid']);
        $field->setAttributes($data, false);
        return $field->save(false);
    }

    /**
     * @inheritDoc
     */
    public static function delete(array $data)
    {
        \Craft::$app->getDb()->createCommand()
            ->delete(FieldRecord::tableName(), ['uid' => $data['uid']])
            ->execute();
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
        if ($handle) {
            Themes::$plugin->fieldDisplayers->ensureDisplayerIsValidForField($handle, $this);
        }
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
        $this->_displayer = Themes::$plugin->fieldDisplayers->getByHandle($this->displayerHandle);
        $this->_displayer->field = $this;
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
        return $this->displayer->options->toArray();
    }

    /**
     * @inheritDoc
     */
    public function setOptions(?array $options)
    {
        if ($this->displayer and $this->displayer->hasOptions) {
            $attributes = $this->displayer->options->safeAttributes();
            $options = array_intersect_key($options, array_flip($attributes));
            $this->displayer->options->setAttributes($options);
        }
    }

    /**
     * @inheritDoc
     */
    public function getAvailableDisplayers(): array
    {
        return Themes::$plugin->fieldDisplayers->getForField(get_class($this));
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        foreach ($this->layout->element->fieldLayout->tabs as $tab) {
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
    public function render(Element $element): string
    {
        return Themes::$plugin->view->renderField($this, $element, $element->{$this->handle});
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