<?php 

namespace Ryssbowh\CraftThemes\models;

use craft\helpers\StringHelper;

class DisplayGroup extends DisplayItem 
{
    public $name;
    public $handle;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name', 'handle'], 'required'],
            [['handle', 'name'], 'string']
        ]);
    }

    /**
     * Project config to be saved
     * 
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'name' => $this->name,
            'handle' => $this->handle,
            'uid' => $this->uid ?? StringHelper::UUID()
        ];
    }
}