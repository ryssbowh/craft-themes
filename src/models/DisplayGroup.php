<?php 

namespace Ryssbowh\CraftThemes\models;

use craft\helpers\StringHelper;

class DisplayGroup extends DisplayItem 
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $handle;

    /**
     * @var array
     */
    public $fields = [];

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            [['name', 'handle'], 'required'],
            [['handle', 'name'], 'string']
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return [
            'name' => $this->name,
            'handle' => $this->handle,
            'uid' => $this->uid ?? StringHelper::UUID(),
            'fields' => array_map(function ($field) {
                return $field->getConfig();
            }, $this->fields)
        ];
    }
}