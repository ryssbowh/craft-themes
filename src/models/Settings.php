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
    public $setConsole = false;

    /**
     * @var ?string
     */
    public $cp;

    /**
     * @var boolean
     */
    public $setCp = false;

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
     * @var string
     */
    public $redirectTo = 'list';

    /**
     * @var boolean
     */
    public $showShortcuts = true;

    /**
     * @inheritdoc
     */
    public function defineRules(): array
    {
        return [
            [['default', 'redirectTo'], 'string'],
            [['eagerLoad', 'devMode', 'showShortcuts'], 'boolean', 'trueValue' => true, 'falseValue' => false]
        ];
    }

    /**
     * Redirect to options getter
     * 
     * @return array
     */
    public function getRedirectToOptions(): array
    {
        return [
            ['value' => 'list', 'label' => \Craft::t('themes', 'Themes')],
            ['value' => 'blocks', 'label' => \Craft::t('themes', 'Blocks')],
            ['value' => 'display', 'label' => \Craft::t('themes', 'Display')],
            ['value' =>'rules', 'label' => \Craft::t('themes', 'Rules')],
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
