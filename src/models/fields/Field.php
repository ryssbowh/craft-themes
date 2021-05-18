<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\models\DisplayItem;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\records\DisplayRecord;
use Ryssbowh\CraftThemes\services\FieldsService;
use craft\base\Element;
use craft\base\Field as BaseField;
use craft\fields\Matrix as CraftMatrix;

abstract class Field extends DisplayItem
{
    public $displayerHandle;
    public $options;
    public $type;
    public $craft_field_id;
    public $craft_field_class;

    protected $_craftField;
    protected $_displayer;

    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['displayerHandle', 'type'], 'string'],
            ['type', 'required'],
            ['options', 'safe'],
            ['type', 'in', 'range' => Themes::$plugin->fields->getValidTypes()]
        ]);
    }

    public static function getType(): string
    {
        return 'field';
    }

    public static function create(array $config): Field
    {
        $class = get_called_class();
        $field = new $class;
        $attributes = $field->safeAttributes();
        $config = array_intersect_key($config, array_flip($attributes));
        $field->setAttributes($config);
        if (!$config['options'] and $field->displayer) {
            $field->options = $field->displayer->options->toArray();
        }
        return $field;
    }

    public static function save(array $data): bool
    {
        $field = Themes::$plugin->fields->getRecordByUid($data['uid']);
        $field->setAttributes($data, false);
        return $field->save(false);
    }

    public static function shouldExistOnLayout(Layout $layout): bool
    {
        return false;
    }

    public static function createNew(?BaseField $craftField = null): Field
    {
        if ($craftField and get_class($craftField) == CraftMatrix::class) {
            return Matrix::createNew($craftField);
        }
        return static::create(static::buildConfig($craftField));
    }

    public static function buildConfig(?BaseField $craftField): array
    {
        $class = get_called_class();
        if ($craftField) {
            $class = get_class($craftField);
        }
        return [
            'type' => get_called_class()::getType(),
            'craft_field_id' => $craftField ? $craftField->id : null,
            'craft_field_class' => $craftField ? get_class($craftField) : null,
            'displayerHandle' => Themes::$plugin->fieldDisplayers->getDefaultHandle($class) ?? ''
        ];
    }

    public function isVisible(): bool
    {
        if ($this->hidden or !$this->displayer) {
            return false;
        }
        return true;
    }

    /**
     * Project config to be saved
     * 
     * @return array
     */
    public function getConfig(): array
    {
        return array_merge(parent::getConfig(), [
            'displayerHandle' => $this->displayerHandle,
            'options' => $this->options,
            'type' => $this->type
        ]);
    }

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

    public function getAvailableDisplayers(): array
    {
        return Themes::$plugin->fieldDisplayers->getForField(get_class($this), $this);
    }

    public function getDisplayName(): string
    {
        return $this->getName();
    }

    public function fields()
    {
        return array_merge(parent::fields(), ['availableDisplayers', 'name', 'handle', 'displayName']);
    }

    /**
     * @inheritDoc
     */
    public function render(Element $element): string
    {
        return Themes::$plugin->view->renderField($this, $element);
    }

    public function __toString()
    {
        return $this->render();
    }
}