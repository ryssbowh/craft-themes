<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\interfaces\DisplayItemInterface;
use Ryssbowh\CraftThemes\interfaces\GroupInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
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
     * @var string
     */
    public $uid;

    /**
     * @var DateTime
     */
    public $dateCreated;

    /**
     * @var DateTime
     */
    public $dateUpdated;

    /**
     * @var DisplayItemInterface
     */
    protected $_item;

    /**
     * @var GroupInterface
     */
    protected $_group;

    /**
     * @var ViewModeInterface
     */
    protected $_viewMode;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['order', 'type'], 'required'],
            [['order', 'group_id'], 'integer'],
            ['type', 'in', 'range' => DisplayService::TYPES],
            [['uid', 'id', 'viewMode', 'viewMode_id', 'dateCreated', 'dateUpdated'], 'safe'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function afterValidate()
    {
        $this->item->validate();
        parent::afterValidate();
    }

    /**
     * @inheritDoc
     */
    public function hasErrors($attribute = null)
    {
        if ($attribute !== null) {
            return parent::hasErrors($attribute);
        }
        if ($this->item->hasErrors()) {
            return true;
        }
        return parent::hasErrors();
    }

    /**
     * @inheritDoc
     */
    public function getErrors($attribute = null)
    {
        if ($attribute == 'item') {
            return $this->item->errors;
        }
        if ($attribute !== null) {
            return parent::getErrors($attribute);
        }
        $errors = parent::getErrors();
        if ($errors2 = $this->item->errors) {
            $errors['item'] = $errors2;
        }
        return $errors;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return [
            'viewMode_id' => $this->viewMode ? $this->viewMode->uid : null,
            'group_id' => $this->group ? $this->group->uid : null,
            'order' => $this->order,
            'type' => $this->type
        ];
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->getItem()->name;
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return $this->getItem()->handle;
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
        return $this->viewMode->layout;
    }

    /**
     * @inheritDoc
     */
    public function getViewMode(): ?ViewModeInterface
    {
        if (is_null($this->_viewMode)) {
            if ($this->viewMode_id) {
                $this->_viewMode = Themes::$plugin->viewModes->getById($this->viewMode_id);
            } elseif ($this->group_id) {
                $this->_viewMode = $this->group->viewMode;
            }
        }
        return $this->_viewMode;
    }

    /**
     * @inheritDoc
     */
    public function setViewMode(?ViewModeInterface $viewMode)
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
    public function getGroup(): ?GroupInterface
    {
        if ($this->_group === null and is_int($this->group_id)) {
            $this->_group = Themes::$plugin->groups->getById($this->group_id);
        }
        return $this->_group;
    }

    /**
     * @inheritDoc
     */
    public function setGroup(?GroupInterface $group)
    {
        $this->_group = $group;
        $this->group_id = null;
        if ($group and $group->id) {
            $this->group_id = $group->id;
        }
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
    public function render(array $params = []): string
    {
        return $this->item->render(...$params);
    }
}