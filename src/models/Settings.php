<?php

namespace Ryssbowh\CraftThemes\models;

use craft\base\Model;

class Settings extends Model
{
    /**
     * @var array
     */
    public $rules = [];

    /**
     * @var ?string
     */
    public $default;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['default', 'string']
        ];
    }

    public function getRules(): array
    {
        return $this->rules ? $this->rules : [];
    }
}
