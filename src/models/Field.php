<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\FieldException;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\RenderableInterface;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use craft\base\Field as CraftField;
use craft\base\Model;
use craft\fieldlayoutelements\TitleField;
use craft\models\FieldLayout;

class Field extends Model implements RenderableInterface
{
    public $id;
    public $viewMode;
    public $field;
    public $displayerHandle;
    public $labelHidden = false;
    public $order;
    public $hidden = false;
    public $visuallyHidden = false;
    public $options;
    public $labelVisuallyHidden = false;
    public $layout;
    public $dateCreated;
    public $dateUpdated;
    public $uid;

    private $_displayer;

    public function rules()
    {
        return [
            [['viewMode', 'labelHidden', 'order', 'hidden', 'visuallyHidden', 'options', 'labelVisuallyHidden', 'layout'], 'required'],
            [['labelHidden', 'hidden', 'visuallyHidden', 'labelVisuallyHidden'], 'boolean'],
            [['viewMode', 'order', 'layout'], 'integer'],
            [['displayerHandle'], 'string'],
        ];
    }

    /**
     * Project config to be saved
     * 
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'viewMode' => $this->viewMode()->uid,
            'field' => $this->field,
            'displayerHandle' => $this->displayerHandle,
            'labelHidden' => $this->labelHidden,
            'order' => $this->order,
            'hidden' => $this->hidden,
            'visuallyHidden' => $this->visuallyHidden,
            'options' => $this->options,
            'labelVisuallyHidden' => $this->labelVisuallyHidden,
            'layout' => $this->layout()->uid
        ];
    }

    public function layout(): Layout
    {
        return Themes::$plugin->layouts->getById($this->layout);
    }

    public function getFieldLayout(): FieldLayout
    {
        return $this->layout()->element()->getFieldLayout();
    }

    public function craftField(): ?CraftField
    {
        if ($this->field == 'title') {
            return null;
        }
        return $this->getFieldLayout()->getField($this->field)->getField();
    }

    public function viewMode(): ViewMode
    {
        return Themes::$plugin->viewModes->getById($this->viewMode);
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
        if ($this->field == 'title') {
            $class = TitleField::class;
        } else {
            $class = get_class($this->craftField());
        }
        return Themes::$plugin->fieldDisplayers->getForField($class);
    }

    public function getName()
    {
        if ($this->field == 'title') {
            return \Craft::t('themes', 'Title');
        }
        return $this->craftField()->name;
    }

    public function fields()
    {
        return array_merge(parent::fields(), ['availableDisplayers', 'name', 'type']);
    }

    public function getOptionsHtml(): string
    {
        return $this->displayer->getOptionsHtml();
    }

    public function getType(): string
    {
        if ($this->field == 'title') {
            return \Craft::t('themes', 'Title');
        }
        return $this->craftField()::displayName();
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