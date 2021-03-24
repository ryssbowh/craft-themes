<?php

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\layouts\Layout;
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

    private $_layout;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'handle', 'layout_id'], 'required'],
            [['name', 'handle'], 'string'],
            ['layout_id', 'integer']
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
            'layout_id' => $this->layout->uid,
            'name' => $this->name,
            'handle' => $this->handle,
        ];
    }

    /**
     * Get layout object
     * 
     * @return Layout
     */
    public function getLayout(): Layout
    {
        if (is_null($this->_layout)) {
            $this->_layout = Themes::$plugin->layouts->getById($this->layout_id);
        }
        return $this->_layout;
    }
}
