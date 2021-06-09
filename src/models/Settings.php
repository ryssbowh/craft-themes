<?php

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\records\ViewModeRecord;
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
     * @var boolean
     */
    public $devMode = false;

    /**
     * @var boolean
     */
    public $eagerLoad = true;

    /**
     * @var boolean
     */
    public $installed = false;

    /**
     * @inheritdoc
     */
    public function defineRules(): array
    {
        return [
            ['default', 'string'],
            [['eagerLoad', 'devMode', 'installed'], 'boolean']
        ];
    }

    /**
     * @inheritDoc
     */
    public function getRules(): array
    {
        return $this->rules ? $this->rules : [];
    }

    /**
     * dev mode enabled getter
     * 
     * @return bool
     */
    public function getDevModeEnabled(): bool
    {
        if (getenv('ENVIRONMENT') != 'production') {
            return $this->devMode;
        }
        return false;
    }
}
