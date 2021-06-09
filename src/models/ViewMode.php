<?php

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use craft\base\Model;

class ViewMode extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $layout_id;

    /**
     * @var name
     */
    public $name;

    /**
     * @var string
     */
    public $handle;

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
     * @var LayoutInterface
     */
    protected $_layout;

    /**
     * @inheritdoc
     */
    public function defineRules(): array
    {
        return [
            [['name', 'handle', 'layout_id'], 'required'],
            [['name', 'handle'], 'string'],
            ['layout_id', 'integer'],
            [['dateCreated', 'dateUpdated', 'uid', 'id'], 'safe']
        ];
    }

    /**
     * Get project config 
     * 
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'name' => $this->name,
            'handle' => $this->handle,
            'uid' => $this->uid
        ];
    }

    /**
     * Get layout object
     * 
     * @return LayoutInterface
     */
    public function getLayout(): LayoutInterface
    {
        if (is_null($this->_layout)) {
            $this->_layout = Themes::$plugin->layouts->getById($this->layout_id);
        }
        return $this->_layout;
    }

    /**
     * Layout setter
     * 
     * @param LayoutInterface $layout
     */
    public function setLayout(LayoutInterface $layout)
    {
        $this->_layout = $layout;
    }
}
