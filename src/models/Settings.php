<?php

namespace Ryssbowh\CraftThemes\models;

use craft\base\Model;

class Settings extends Model
{
    /**
     * @var array
     */
    public $themes = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['themes', 'each', 'rule' => ['string']]
        ];
    }

    /**
     * Get the theme handle for a site
     * 
     * @param  string $uid
     * @return string
     */
    public function getHandle(string $uid): string
    {
        return $this->themes[$uid] ?? '';
    }
}
