<?php
namespace Ryssbowh\CraftThemes;

use Craft;
use Ryssbowh\CraftThemes\assets\SettingsAssets;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\Settings;
use Ryssbowh\CraftThemes\services\ThemesRegistry;
use Ryssbowh\CraftThemes\services\ThemesRules;
use Ryssbowh\CraftThemes\twig\TwigTheme;
use craft\base\PluginInterface;
use craft\events\PluginEvent;
use craft\events\RegisterCacheOptionsEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\TemplateEvent;
use craft\helpers\App;
use craft\services\Plugins;
use craft\utilities\ClearCaches;
use craft\web\UrlManager;
use craft\web\View;
use yii\base\Event;
use yii\log\Logger;
use craft\web\Request;

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
        $_this = $this;

        \Yii::setAlias('@themesPath', '@root/themes');
        \Yii::setAlias('@themesWebPath', '@webroot/themes');

        $this->setComponents([
            'registry' => ThemesRegistry::class,
            'rules' => [
                'class' => ThemesRules::class,
                'rules' => $this->getSettings()->getRules(),
                'default' => $this->getSettings()->default
            ]
        ]);

        Craft::$app->view->registerTwigExtension(new TwigTheme);

        Event::on(ClearCaches::class, ClearCaches::EVENT_REGISTER_CACHE_OPTIONS,
            function (RegisterCacheOptionsEvent $event) {
                $event->options[] = [
                    'key' => 'themes-cache',
                    'label' => Craft::t('themes', 'Themes cache'),
                    'action' => function() {
                        ThemesRules::clearCaches();
                    }
                ];
            }
        );

        Event::on(Plugins::class, Plugins::EVENT_AFTER_ENABLE_PLUGIN,
            function (PluginEvent $event) {
                ThemesRules::clearCaches();
            }
        );

        Event::on(Plugins::class, Plugins::EVENT_AFTER_DISABLE_PLUGIN,
            function (PluginEvent $event) {
                ThemesRules::clearCaches();
            }
        );

        Event::on(Plugins::class, Plugins::EVENT_BEFORE_INSTALL_PLUGIN,
            function (PluginEvent $event) use ($_this) {
                $_this->installDependency($event->plugin);
            }
        );

        \Craft::info('Loaded themes plugin', __METHOD__);

        Event::on(View::class, View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS, function ($e) use ($_this) {
            \Craft::info('Resolving current theme', __METHOD__);
            Themes::$plugin->resolveTheme($e);
        });
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
    protected function resolveTheme(RegisterTemplateRootsEvent $event)
    {
        $theme = $this->rules->resolveCurrentTheme();
        $this->registry->setCurrent($theme);

        if (!$theme) {
            \Craft::info("No theme found for request", __METHOD__);
            return;
        }

        \Yii::setAlias('@themePath', '@root/themes/' . $theme->handle);
        \Yii::setAlias('@themeWebPath', '@webroot/themes/' . $theme->handle);
        $event->roots[''] = array_merge($theme->getTemplatePaths(), $event->roots[''] ?? []);
        if (\Craft::$app->request instanceof Request) {
            $path = \Craft::$app->request->getPathInfo(); 
            $theme->registerAssetBundles($path);
        }
        \Craft::info("Theme has been set to : " . $theme->name, __METHOD__);
    }

    /**
     * Install parent theme
     * 
     * @param  PluginInterface $plugin
     */
    protected function installDependency(PluginInterface $plugin)
    {
        if (!$plugin instanceof ThemeInterface) {
            return;
        }
        if ($parent = $plugin->getExtends()) {
            \Craft::$app->plugins->installPlugin($parent);
        }
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
        $themes = $this->registry->getNonPartials(true);
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
