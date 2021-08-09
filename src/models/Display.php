<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\interfaces\DisplayItemInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Group;
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
    public $order = 0;

    /**
     * @var string
     */
    public $type;

    /**
     * @var int
     */
    public $viewMode_id;

    /**
     * @var int
     */
    public $group_id;

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
     * @var DisplayItemInterface
     */
    protected $_item;

    /**
     * @var Group
     */
    protected $_group;

    /**
     * @var ViewMode
     */
    protected $_viewMode;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['order', 'type'], 'required'],
            [['viewMode_id', 'order', 'group_id'], 'integer'],
            ['type', 'in', 'range' => DisplayService::TYPES],
            [['dateCreated', 'dateUpdated', 'uid', 'id', 'viewMode'], 'safe'],
            ['viewMode', function () {
                if (!$this->viewMode) {
                    $this->addError('viewMode', \Craft::t('View mode is required'));
                }
            }]
        ];
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return [
            'viewMode_id' => $this->viewMode->uid,
            'group_id' => $this->group ? $this->group->uid : null,
            'order' => $this->order,
            'type' => $this->type
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
                $this->_item = Themes::$plugin->groups->getForDisplay($this);
            } else {
                $this->_item = Themes::$plugin->fields->getForDisplay($this);
            }
        }
        return $this->_item;
    }

    /**
     * @inheritDoc
     */
    public function setItem(DisplayItemInterface $item)
    {
        $this->_item = $item;
    }

    /**
     * @inheritDoc
     */
    public function getGroup(): ?DisplayInterface
    {
        if ($this->group_id === null) {
            return null;
        }
        if ($this->_group === null) {
            if (is_int($this->group_id)) {
                $this->_group = Themes::$plugin->displays->getById($this->group_id);
            } else {
                $this->_group = Themes::$plugin->displays->getByUid($this->group_id);
            }
        }
        return $this->_group;
    }

    /**
     * @inheritDoc
     */
    public function setGroup(DisplayInterface $group)
    {
        $this->_group = $group;
    }

    /**
     * @inheritDoc
     */
    public function isGroup(): bool
    {
        return $this->type == DisplayService::TYPE_GROUP;
    }

    /**
     * @inheritDoc
     */
    public function render(Element $element): string
    {
        return $this->item->render($element);
    }
}