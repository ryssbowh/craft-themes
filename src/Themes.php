<?php
namespace Ryssbowh\CraftThemes;

use Craft;
use Ryssbowh\CraftThemes\assets\SettingsAssets;
use Ryssbowh\CraftThemes\models\Settings;
use Ryssbowh\CraftThemes\services\ThemesRegistry;
use Ryssbowh\CraftThemes\services\ThemesRules;
use Ryssbowh\CraftThemes\twig\TwigTheme;
use craft\events\RegisterCacheOptionsEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\TemplateEvent;
use craft\helpers\App;
use craft\utilities\ClearCaches;
use craft\web\UrlManager;
use craft\web\View;
use yii\base\Event;
use yii\log\Logger;

class Themes extends \craft\base\Plugin
{
    public static $plugin;
    
    /**
     * @inheritdoc
     */
    public $hasCpSettings = true;

    /**
     * Initializes the plugin.
     */
    public function init()
    {
        parent::init();

        self::$plugin = $this;

        \Yii::setAlias('@themesPath', '@root/themes');
        \Yii::setAlias('@themesWebPath', '@webroot/themes');

        $this->setComponents([
            'registry' => [
                'class' => ThemesRegistry::class,
                'folder' => \Yii::getAlias('@themesPath'),
            ],
            'rules' => [
                'class' => ThemesRules::class,
                'rules' => $this->getSettings()->rules,
                'default' => $this->getSettings()->default
            ]
        ]);

        Event::on(ClearCaches::class, ClearCaches::EVENT_REGISTER_CACHE_OPTIONS,
            function (RegisterCacheOptionsEvent $event) {
                $event->options[] = [
                    'key' => 'themes-cache',
                    'label' => Craft::t('themes', 'Themes cache'),
                    'action' => function() {
                        ThemesRegistry::clearCaches();
                        ThemesRules::clearCaches();
                    }
                ];
            }
        );

        \Craft::info('Loaded themes plugin, handling current request...', __METHOD__);

        if (!Craft::$app->request->getIsSiteRequest()) {
            return ;
        }

        $this->handleCurrentRequest();
    }

    /**
     * Clear rule cache adter saving settings
     */
    public function afterSaveSettings()
    {
        parent::afterSaveSettings();
        ThemesRules::clearCaches();
    }

    /**
     * Resolves current theme and registers aliases, templates & hooks
     */
    protected function handleCurrentRequest()
    {
        $theme = $this->rules->resolveCurrentTheme();
        $this->registry->setCurrent($theme);

        if (!$theme) {
            \Craft::info("No theme found for request ".\Craft::$app->request->getUrl(), __METHOD__);
            return;
        }

        Craft::$app->view->registerTwigExtension(new TwigTheme);

        //Register templates event hook
        Event::on(
            View::class,
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
            function(RegisterTemplateRootsEvent $event) use ($theme) {
                $event->roots[''] = array_merge($theme->getTemplatePaths(), $event->roots[''] ?? []);
            }
        );
        //Register bundle assets event hook
        Event::on(
            View::class,
            View::EVENT_BEFORE_RENDER_PAGE_TEMPLATE,
            function(TemplateEvent $event) use ($theme) {
                $path = \Craft::$app->request->getPathInfo();
                $theme->registerAssetBundles($path);
            }
        );
    }

    /**
     * Parse all sites and languages, return an array
     * [
     *     [
     *         'uid' => 'Site name'
     *     ],
     *     [
     *         'en-GB' => 'English'
     *     ]
     * ]
     * @return [type] [description]
     */
    protected function parseSites(): array
    {
        $sites = [];
        $languages = [];
        foreach (\Craft::$app->sites->getAllSites() as $site) {
            $sites[$site->uid] = $site->name;
            $locale = $site->getLocale();
            if ($locale->id and !in_array($locale->id, $languages)) {
                $languages[$locale->id] = $locale->getDisplayName();
            }
        }
        return [$sites, $languages];
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        \Craft::$app->view->registerAssetBundle(SettingsAssets::class);
        $themes = $this->registry->getAsNames();
        list($sites, $languages) = $this->parseSites();
        $cols = [
            'enabled' => [
                'heading' => \Craft::t('themes', 'Enabled'),
                'type' => 'lightswitch',
                'class' => 'thin enabled'
            ],
            'url' => [
                'type' => 'type',
                'heading' => \Craft::t('themes', 'Path (or regex)'),
                'class' => 'url cell',
                'placeholder' => \Craft::t('themes', 'Enter path here')
            ],
            'site' => [
                'heading' => \Craft::t('themes', 'Site'),
                'type' => 'select',
                'options' => ['' => \Craft::t('themes', 'Any')] + $sites,
                'class' => 'site cell'
            ],
            'language' => [
                'heading' => \Craft::t('themes', 'Language'),
                'type' => 'select',
                'options' => ['' => \Craft::t('themes', 'Any')] + $languages,
                'class' => 'language cell'
            ],
            'theme' => [
                'heading' => \Craft::t('themes', 'Theme'),
                'type' => 'select',
                'options' => $themes,
                'class' => 'theme cell'
            ]
        ];
        return Craft::$app->view->renderTemplate('themes/_settings', [
            'settings' => $this->getSettings(),
            'cols' => $cols,
            'themes' => ['' => \Craft::t('themes', 'No theme')] + $themes,
            'settings' => $this->getSettings()
        ]);
    }
}
