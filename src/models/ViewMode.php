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
    public $layout;

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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'handle'], 'string'],
            ['layout', 'integer']
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
            'layout' => $this->layout()->uid,
            'name' => $this->name,
            'handle' => $this->handle,
        ];
    }

    /**
     * Get layout object
     * 
     * @return Layout
     */
    public function layout(): Layout
    {
        return Themes::$plugin->layouts->getById($this->layout);
    }
}
