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

    public $memoryLoading = true;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['default', 'string'],
            [['eagerLoad', 'devMode', 'memoryLoading'], 'boolean']
        ];
    }

    /**
     * @inheritDoc
     */
    public function getRules(): array
    {
        return $this->rules ? $this->rules : [];
    }

    public function getDevModeEnabled(): bool
    {
        if (getenv('ENVIRONMENT') != 'production') {
            return $this->devMode;
        }
        return false;
    }
}
