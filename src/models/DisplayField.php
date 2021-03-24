<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\FieldException;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\RenderableInterface;
use Ryssbowh\CraftThemes\models\DisplayField;
use craft\base\Field;
use craft\base\Model;
use craft\fieldlayoutelements\TitleField;
use craft\helpers\StringHelper;
use craft\models\FieldLayout;

class DisplayField extends DisplayItem implements RenderableInterface
{
    public $fieldUid;
    public $displayerHandle;
    public $display_id = null;
    public $options;

    private $_displayer;
    private $_craftField;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['options', 'fieldUid'], 'required'],
            [['displayerHandle', 'fieldUid'], 'string']
        ]);
    }

    /**
     * Project config to be saved
     * 
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'fieldUid' => $this->fieldUid,
            'displayerHandle' => $this->displayerHandle,
            'options' => $this->options,
            'uid' => $this->uid ?? StringHelper::UUID()
        ];
    }

    public function getFieldLayout(): FieldLayout
    {
        return $this->layout->element()->getFieldLayout();
    }

    public function getCraftField(): ?Field
    {
        if ($this->fieldUid == 'title') {
            return null;
        }
        if ($this->_craftField === null) {
            foreach ($this->getFieldLayout()->getFields() as $field) {
                if ($field->uid === $this->fieldUid) {
                    $this->_craftField = $field;
                } 
            }
        }
        return $this->_craftField;
    }

    public function getHandle(): string
    {
        if ($this->fieldUid == 'title') {
            return 'title';
        }
        return $this->craftField->handle;
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
        if ($this->fieldUid == 'title') {
            $class = TitleField::class;
        } else {
            $class = get_class($this->craftField);
        }
        return Themes::$plugin->fieldDisplayers->getForField($class);
    }

    public function getName(): string
    {
        if ($this->fieldUid == 'title') {
            return \Craft::t('themes', 'Title');
        }
        return $this->craftField->name;
    }

    public function fields()
    {
        return array_merge(parent::fields(), ['availableDisplayers', 'name', 'type', 'handle']);
    }

    public function getOptionsHtml(): string
    {
        return $this->displayer->getOptionsHtml();
    }

    public function getType(): string
    {
        if ($this->fieldUid == 'title') {
            return \Craft::t('themes', 'Title');
        }
        return $this->craftField::displayName();
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return Themes::$plugin->view->renderField($this);
    }

    public function __toString()
    {
        return $this->render();
    }
}