<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\services\DisplayService;
use craft\base\Element;
use craft\base\Model;

class Display extends Model 
{
    public $id;
    public $order;
    public $type;
    public $viewMode_id;
    public $dateCreated;
    public $dateUpdated;
    public $uid;

    protected $_viewMode;
    protected $_item;

    public function defineRules(): array
    {
        return [
            [['viewMode_id', 'order', 'type'], 'required'],
            [['viewMode_id', 'order'], 'integer'],
            ['type', 'in', 'range' => DisplayService::TYPES],
            [['dateCreated', 'dateUpdated', 'uid', 'id', 'viewMode'], 'safe']
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
            'order' => $this->order,
            'type' => $this->type,
            'uid' => $this->uid,
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
        if ($this->_item === null) {
            if ($this->type == DisplayService::TYPE_GROUP) {
                $this->_item = Themes::$plugin->groups->getForDisplay($this->id);
            } else {
                $this->_item = Themes::$plugin->fields->getForDisplay($this->id);
            }
        }
        return $this->_item;
    }

    public function setItem(?DisplayItem $item)
    {
        $this->_item = $item;
    }

    public function render(Element $element): string
    {
        return $this->item->render($element);
    }

    public function __toString()
    {
        return $this->render();
    }
}