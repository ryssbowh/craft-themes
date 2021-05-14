<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\models\DisplayItem;
use Ryssbowh\CraftThemes\services\FieldsService;
use craft\base\Element;

class Field extends DisplayItem
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
            ['options', 'each', 'rule' => ['safe', 'skipOnEmpty' => false]],
            ['type', 'required'],
            ['type', 'in', 'range' => FieldsService::TYPES]
        ]);
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

    public function getHandle(): string
    {
        return 'title';
    }

    public function getDisplayer(): ?FieldDisplayerInterface
    {
        if (!is_null($this->_displayer)) {
            return $this->_displayer;
        }
        if (!$this->displayerHandle) {
            return null;
        }
        $displayer = Themes::$plugin->fieldDisplayers->getByHandle($this->displayerHandle);
        $displayer->field = $this;
        return $displayer;
    }

    public function getAvailableDisplayers(): array
    {
        return Themes::$plugin->fieldDisplayers->getForField(TitleField::class);
    }

    public function getName(): string
    {
        return \Craft::t('themes', 'Title');
    }

    public function getDisplayName(): string
    {
        return $this->getName();
    }

    public function fields()
    {
        return array_merge(parent::fields(), ['availableDisplayers', 'name', 'handle', 'displayName']);
    }

    public function getOptionsHtml(): string
    {
        return $this->displayer->getOptionsHtml();
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