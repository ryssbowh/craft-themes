<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\services\DisplayService;
use craft\base\Model;

class Display extends Model 
{
    public $id;
    public $type;
    public $order;
    public $viewMode_id;
    public $hidden = false;
    public $visuallyHidden = false;
    public $labelHidden = false;
    public $labelVisuallyHidden = false;
    public $dateCreated;
    public $dateUpdated;
    public $uid;

    protected $_viewMode;
    protected $_item;

    public function rules()
    {
        return [
            [['type', 'viewMode_id', 'order', 'labelHidden', 'hidden', 'visuallyHidden', 'labelVisuallyHidden'], 'required'],
            [['labelHidden', 'hidden', 'visuallyHidden', 'labelVisuallyHidden'], 'boolean'],
            [['viewMode_id', 'order'], 'integer'],
            ['type', 'string'],
            ['type', 'in', 'range' => [DisplayService::TYPE_MATRIX, DisplayService::TYPE_GROUP, DisplayService::TYPE_FIELD]]
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
            'viewMode_id' => $this->viewMode->uid,
            'type' => $this->type,
            'order' => $this->order,
            'labelHidden' => $this->labelHidden,
            'hidden' => $this->hidden,
            'visuallyHidden' => $this->visuallyHidden,
            'labelVisuallyHidden' => $this->labelVisuallyHidden,
            'item' => $this->item->getConfig()
        ];
    }

    public function fields()
    {
        return array_merge(parent::fields(), ['item']);
    }

    public function getLayout(): Layout
    {
        return $this->viewMode->layout();
    }

    public function getViewMode(): ViewMode
    {
        if (is_null($this->_viewMode)) {
            $this->_viewMode = Themes::$plugin->viewModes->getById($this->viewMode_id);
        }
        return $this->_viewMode;
    }

    public function setViewMode(ViewMode $viewMode)
    {
        $this->_viewMode = $viewMode;
    }

    public function getItem(): DisplayItem
    {
        if (!$this->_item) {
            switch ($this->type) {
                case 'field':
                    $this->_item = new DisplayField;
                    break;
                case 'group':
                    $this->_item = new DisplayGroup;
                    break;
                case 'matrix':
                    $this->_item = new DisplayMatrix;
                    break;
            }
        }
        return $this->_item;
    }

    public function setItem(DisplayItem $item)
    {
        $this->_item = $item;
    }

    public function render(): string
    {
        return $this->item->render();
    }

    public function __toString()
    {
        return $this->render();
    }
}