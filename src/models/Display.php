<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\interfaces\DisplayItemInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\services\DisplayService;
use craft\base\Element;
use craft\base\Model;

class Display extends Model implements DisplayInterface
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $order;

    /**
     * @var string
     */
    public $type;

    /**
     * @var int
     */
    public $viewMode_id;

    /**
     * @var DateTime
     */
    public $dateCreated;

    /**
     * @var DateTime
     */
    public $dateUpdated;

    /**
     * @var string
     */
    public $uid;

    /**
     * @var array
     */
    protected $_viewMode;

    /**
     * @var DisplayItemInterface
     */
    protected $_item;

    /**
     * @inheritDoc
     */
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
     * @inheritDoc
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

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['item']);
    }

    /**
     * @inheritDoc
     */
    public function getLayout(): LayoutInterface
    {
        return $this->viewMode->layout();
    }

    /**
     * @inheritDoc
     */
    public function getViewMode(): ViewMode
    {
        if (is_null($this->_viewMode)) {
            $this->_viewMode = Themes::$plugin->viewModes->getById($this->viewMode_id);
        }
        return $this->_viewMode;
    }

    /**
     * @inheritDoc
     */
    public function setViewMode(ViewMode $viewMode)
    {
        $this->_viewMode = $viewMode;
    }

    /**
     * @inheritDoc
     */
    public function getItem(): DisplayItemInterface
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

    /**
     * @inheritDoc
     */
    public function setItem(?DisplayItemInterface $item)
    {
        $this->_item = $item;
    }

    /**
     * @inheritDoc
     */
    public function render(Element $element): string
    {
        return $this->item->render($element);
    }
}