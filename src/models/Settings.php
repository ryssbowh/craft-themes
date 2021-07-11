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
     * @var ?string
     */
    public $console;

    /**
     * @var boolean
     */
    public $setConsole = true;

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
    public $blockCache;

    /**
     * @var boolean
     */
    public $templateCache;

    /**
     * @var boolean
     */
    public $rulesCache;

    /**
     * @inheritdoc
     */
    public function defineRules(): array
    {
        return [
            ['default', 'string'],
            [['eagerLoad', 'devMode'], 'boolean']
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

    /**
     * block cache enabled getter
     * 
     * @return bool
     */
    public function getBlockCacheEnabled(): bool
    {
        if (!is_null($this->blockCache)) {
            return $this->blockCache;
        }
        return !\Craft::$app->getConfig()->getGeneral()->devMode;
    }

    /**
     * template cache enabled getter
     * 
     * @return bool
     */
    public function getTemplateCacheEnabled(): bool
    {
        if (!is_null($this->templateCache)) {
            return $this->templateCache;
        }
        return !\Craft::$app->getConfig()->getGeneral()->devMode;
    }

    /**
     * rules cache enabled getter
     * 
     * @return bool
     */
    public function getRulesCacheEnabled(): bool
    {
        if (!is_null($this->rulesCache)) {
            return $this->rulesCache;
        }
        return !\Craft::$app->getConfig()->getGeneral()->devMode;
    }
}
