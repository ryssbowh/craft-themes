<?php 

namespace Ryssbowh\CraftThemes\models;

use craft\helpers\StringHelper;

class DisplayMatrix extends DisplayItem 
{
    public $fieldUid;
    public $displayerHandle;
    public $options;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['fieldUid', 'displayerHandle'], 'required'],
            [['displayerHandle', 'fieldUid'], 'string'],
            ['options', 'default', 'value' => []]
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
            'fieldUid' => $this->fieldUid,
            'displayerHandle' => $this->displayerHandle,
            'options' => $this->options,
            'uid' => $this->uid ?? StringHelper::UUID()
        ];
    }
}