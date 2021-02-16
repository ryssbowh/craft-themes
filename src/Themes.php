<?php
namespace Ryssbowh\CraftThemes;

use Craft;
use Ryssbowh\CraftThemes\assets\SettingsAssets;

class Themes extends \craft\base\Plugin
{   
    /**
     * @var Themes
     */
    public static $plugin;

    /**
     * @inheritdoc
     */
    public $schemaVersion = '1.0.0';
    
    /**
     * @inheritdoc
     */
    public $hasCpSettings = true;

    /**
     * inheritDoc
     */
    public function init()
    {
        parent::init();

        self::$plugin = $this;
        $_this = $this;

        \Yii::setAlias('@themesPath', '@root/themes');
        \Yii::setAlias('@themesWebPath', '@webroot/themes');

        $this->registerServices();
        $this->registerClearCacheEvent();
        $this->registerBlockProviders();
        $this->registerProjectConfig();

        \Craft::info('Loaded themes plugin, handling current request...', __METHOD__);

        if (Craft::$app->request->getIsSiteRequest()) {
            $theme = $this->handleCurrentRequest();
            if ($theme) {
                $this->registerFrontTemplates($theme);
            }
        }
        if (Craft::$app->request->getIsCpRequest()) {
            $this->registerNavItem();
            $this->registerCpRoutes();
        }
    }

    /**
     * Clear rule cache adter saving settings
     */
    public function afterSaveSettings()
    {
        parent::afterSaveSettings();
        ThemesRules::clearCaches();
    }

    protected function registerFrontTemplates(ThemeInterface $theme)
    {
        Event::on(View::class, View::EVENT_BEFORE_RENDER_PAGE_TEMPLATE, function(TemplateEvent $event) use ($theme) {
            $event->variables['layout'] = $theme->getPageLayout();
        });
        Event::on(View::class, View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS, function (RegisterTemplateRootsEvent $event) {
            $event->roots[''][] = __DIR__ . '/templates/front';
        });
    }

    /**
     * Register cp routes
     */
    protected function registerCpRoutes()
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            if (\Craft::$app->config->getGeneral()->allowAdminChanges) {
                $event->rules = array_merge($event->rules, [
                    'themes/config/<themeName:\w+>' => 'themes/cp-config',
                    'themes/display/<themeName:\w+>' => 'themes/cp-display',
                    'themes/blocks' => 'themes/cp-layout',
                    'themes/blocks/<themeName:\w+>' => 'themes/cp-layout',
                    'themes/blocks/<themeName:\w+>/save' => 'themes/cp-layout/save',
                ]);
            }
        });
    }

    /**
     * Register services
     */
    protected function registerServices()
    {
        $this->setComponents([
            'registry' => ThemesRegistry::class,
            'rules' => [
                'class' => ThemesRules::class,
                'rules' => $this->getSettings()->getRules(),
                'default' => $this->getSettings()->default
            ],
            'layouts' => LayoutService::class,
            'blockProviders' => BlockProvidersService::class,
            'blocks' => BlockService::class
        ]);
    }

    protected function registerBlockProviders()
    {
        Event::on(BlockProvidersService::class, BlockProvidersService::REGISTER_BLOCK_PROVIDERS, function (RegisterBlockProvidersEvent $event) {
            $event->add(new SystemBlockProvider);
        });
    }

    /**
     * Clear cache event subscription
     */
    protected function registerClearCacheEvent()
    {
        Event::on(ClearCaches::class, ClearCaches::EVENT_REGISTER_CACHE_OPTIONS, function (RegisterCacheOptionsEvent $event) {
            $event->options[] = [
                'key' => 'themes-cache',
                'label' => Craft::t('themes', 'Themes cache'),
                'action' => function() {
                    ThemesRegistry::clearCaches();
                    ThemesRules::clearCaches();
                }
            ];
        });

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

        if (Craft::$app->request->getIsSiteRequest()) {
            $_this = $this;
            Event::on(View::class, View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS, function ($e) use ($_this) {
                \Craft::info('Resolving current theme', __METHOD__);
                $_this->resolveTheme($e);
            });
        }
    }

    /**
     * Registers cp nav items
     */
    protected function registerNavItem()
    {
        if (\Craft::$app->getRequest()->isCpRequest) {
            $_this = $this;
            Event::on(Cp::class, Cp::EVENT_REGISTER_CP_NAV_ITEMS, function(RegisterCpNavItemsEvent $event) use ($_this){
                if (\Craft::$app->config->getGeneral()->allowAdminChanges) {
                    $event->navItems[] = [
                        'url' => 'themes/config',
                        'label' => \Craft::t('themes', 'Theming'),
                        'subnav' => [
                            'themes-config' => [
                                'url' => 'themes/config',
                                'label' => \Craft::t('themes', 'Config'),
                            ],
                            'themes-display' => [
                                'url' => 'themes/display',
                                'label' => \Craft::t('themes', 'Display'),
                            ],
                            'themes-blocks' => [
                                'url' => 'themes/blocks',
                                'label' => \Craft::t('themes', 'Blocks'),
                            ]
                        ]
                    ];
                }
            });
        }
    }

    protected function registerProjectConfig()
    {
        Craft::$app->projectConfig
            ->onAdd(BlockService::CONFIG_KEY.'.{uid}',      [$this->blocks,   'handleChanged'])
            ->onUpdate(BlockService::CONFIG_KEY.'.{uid}',   [$this->blocks,   'handleChanged'])
            ->onRemove(BlockService::CONFIG_KEY.'.{uid}',   [$this->blocks,   'handleDeleted']);

        Event::on(ProjectConfig::class, ProjectConfig::EVENT_REBUILD, function(RebuildConfigEvent $e) {
            Themes::$plugin->blocks->rebuildLayoutConfig($e);
        });
    }

    /**
     * Resolves current theme and registers aliases, templates & hooks
     */
    protected function resolveTheme(RegisterTemplateRootsEvent $event)
    {
        $theme = $this->rules->resolveCurrentTheme();
        $this->registry->setCurrent($theme);

        if (!$theme) {
            \Craft::info("No theme found for request ".\Craft::$app->request->getUrl(), __METHOD__);
            return null;
        }

        \Yii::setAlias('@themePath', '@root/themes/' . $theme->handle);
        \Yii::setAlias('@themeWebPath', '@webroot/themes/' . $theme->handle);
        Craft::$app->view->registerTwigExtension(new TwigTheme);
        $event->roots[''] = array_merge($theme->getTemplatePaths(), $event->roots[''] ?? []);
        $path = \Craft::$app->request->getPathInfo();
        $theme->registerAssetBundles($path);
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
     * Parse all sites and languages, returns an array
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
        return Craft::$app->view->renderTemplate('themes/cp/settings', [
            'settings' => $this->getSettings(),
            'cols' => $cols,
            'themes' => ['' => \Craft::t('themes', 'No theme')] + $themes,
            'settings' => $this->getSettings()
        ]);
    }
}
