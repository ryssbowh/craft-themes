<?php

namespace Ryssbowh\CraftThemes\models;

use craft\base\Model;

class ViewMode extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $theme;

    /**
     * @var string
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
            [['theme', 'layout', 'name', 'handle'], 'string']
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
            'theme' => $this->theme,
            'layout' => $this->layout,
            'name' => $this->name,
            'handle' => $this->handle,
        ];
    }
}
