<?php

namespace Ryssbowh\Themes\models;

use craft\base\Model;

class Settings extends Model
{
    /**
     * @var string|null
     */
    public $adminTheme;

    /**
     * @var string|null
     */
    public $siteTheme;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['siteTheme'], 'string']
        ];
    }
}
