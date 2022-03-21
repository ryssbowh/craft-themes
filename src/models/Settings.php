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
     * @var string
     * @since 3.1.0
     */
    public $folder = 'themes';

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
    public $eagerLoad = true;

    /**
     * @var boolean
     */
    public $maxEagerLoadLevel = 5;

    /**
     * @var boolean
     */
    public $hideEmptyFields = true;

    /**
     * @var boolean
     */
    public $blockCache;

    /**
     * @var boolean
     */
    public $displayerCache;

    /**
     * @var boolean
     */
    public $templateCache;

    /**
     * @var boolean
     */
    public $rulesCache;

    /**
     * @var boolean
     */
    public $eagerLoadingCache;

    /**
     * @var string
     */
    public $redirectTo = 'list';

    /**
     * @var boolean
     */
    public $showCpShortcuts = true;

    /**
     * @var string
     */
    public $menuItemName;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (is_null($this->menuItemName)) {
            $this->menuItemName = \Craft::t('themes', 'Theming');
        }
    }

    /**
     * @inheritdoc
     */
    public function defineRules(): array
    {
        return [
            [['showCpShortcuts', 'hideEmptyFields'], 'boolean'],
            [['redirectTo', 'menuItemName'], 'string'],
            ['folder', 'required'],
            ['folder', 'validateFolder']
        ];
    }

    /**
     * Validate the folder setting
     */
    public function validateFolder()
    {
        if (!file_exists(\Craft::getAlias('@root/' . $this->folder))) {
            $this->addError('folder', \Craft::t('themes', 'This folder doesn\'t exist'));
        }
    }

    /**
     * Redirect to options getter
     * 
     * @return array
     */
    public function getRedirectToOptions(): array
    {
        $values = [
            ['value' => 'list', 'label' => \Craft::t('themes', 'Themes')],
            ['value' =>'rules', 'label' => \Craft::t('themes', 'Rules')],
        ];
        if (Themes::$plugin->is(Themes::EDITION_PRO)) {
            $values[] = ['value' => 'display', 'label' => \Craft::t('themes', 'Display')];
            $values[] = ['value' => 'blocks', 'label' => \Craft::t('themes', 'Blocks')];
        }
        return $values;
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
     * field cache enabled getter
     * 
     * @return bool
     */
    public function getDisplayerCacheEnabled(): bool
    {
        if (!is_null($this->displayerCache)) {
            return $this->displayerCache;
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

    /**
     * Eager loading cache enabled getter
     * 
     * @return bool
     */
    public function getEagerLoadingCacheEnabled(): bool
    {
        if (!is_null($this->eagerLoadingCache)) {
            return $this->eagerLoadingCache;
        }
        return !\Craft::$app->getConfig()->getGeneral()->devMode;
    }
}
