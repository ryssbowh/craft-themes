<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\DisplayItem;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\records\FieldRecord;
use Ryssbowh\CraftThemes\services\FieldsService;
use craft\base\Element;
use craft\base\Field as BaseField;
use craft\fieldlayoutelements\CustomField;
use craft\fields\Matrix as CraftMatrix;

abstract class Field extends DisplayItem implements FieldInterface
{
    /**
     * @var string
     */
    public $displayerHandle;

    /**
     * @var array
     */
    public $options;

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
        return 'field';
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
        if ((!isset($config['options']) or !$config['options']) and $field->displayer) {
            $field->options = $field->displayer->options->toArray();
        }
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
    public function delete()
    {
        \Craft::$app->getDb()->createCommand()
            ->delete(FieldRecord::tableName(), ['id' => $this->id])
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
    public function getDisplayer(): ?FieldDisplayerInterface
    {
        if (!is_null($this->_displayer)) {
            return $this->_displayer;
        }
        if (!$this->displayerHandle) {
            return null;
        }
        $this->_displayer = Themes::$plugin->fieldDisplayers->getByHandle($this->displayerHandle, $this);
        return $this->_displayer;
    }

    /**
     * @inheritDoc
     */
    public function getAvailableDisplayers(): array
    {
        return Themes::$plugin->fieldDisplayers->getForField(get_class($this), $this);
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
        return array_merge(parent::fields(), ['availableDisplayers', 'name', 'handle', 'displayName']);
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