<?php

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\records\ViewModeRecord;
use craft\base\Model;

/**
 * Themes plugin settings
 */
class Settings extends Model
{
    /**
     * @var array
     */
    public $themesRules = [];

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
        $values = [
            ['value' => 'display', 'label' => \Craft::t('themes', 'Display')],
            ['value' =>'rules', 'label' => \Craft::t('themes', 'Rules')],
        ];
        if (Themes::$plugin->is(Themes::EDITION_PRO)) {
            $values[] = ['value' => 'list', 'label' => \Craft::t('themes', 'Themes')];
            $values[] = ['value' => 'blocks', 'label' => \Craft::t('themes', 'Blocks')];
        }
        return $values;
    }

    /**
     * dev mode enabled getter, always returns false if environment is 'production'
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
