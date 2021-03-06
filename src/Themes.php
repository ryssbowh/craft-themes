<?php
namespace Ryssbowh\CraftThemes;

use Craft;
use Ryssbowh\CraftThemes\assets\SettingsAssets;
use Ryssbowh\CraftThemes\events\FieldDisplayerEvent;
use Ryssbowh\CraftThemes\events\RegisterBlockProvidersEvent;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\Settings;
use Ryssbowh\CraftThemes\models\SystemBlockProvider;
use Ryssbowh\CraftThemes\services\BlockProvidersService;
use Ryssbowh\CraftThemes\services\BlockService;
use Ryssbowh\CraftThemes\services\FieldsService;
use Ryssbowh\CraftThemes\services\LayoutService;
use Ryssbowh\CraftThemes\services\ThemesRegistry;
use Ryssbowh\CraftThemes\services\ThemesRules;
use Ryssbowh\CraftThemes\services\ViewModeService;
use Ryssbowh\CraftThemes\twig\TwigTheme;
use craft\base\PluginInterface;
use craft\events\PluginEvent;
use craft\events\RebuildConfigEvent;
use craft\events\RegisterCacheOptionsEvent;
use craft\events\RegisterCpNavItemsEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\TemplateEvent;
use craft\helpers\UrlHelper;
use craft\services\Categories;
use craft\services\Plugins;
use craft\services\ProjectConfig;
use craft\services\Routes;
use craft\services\Sections;
use craft\utilities\ClearCaches;
use craft\web\UrlManager;
use craft\web\View;
use craft\web\twig\variables\Cp;
use yii\base\Event;

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
        $this->registerPluginsEvents();
        $this->registerElementsEvents();

        if (Craft::$app->request->getIsSiteRequest()) {
            Event::on(View::class, View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS, function ($e) use ($_this) {
                $_this->resolveTheme($e);
            });
        }

        if (Craft::$app->request->getIsCpRequest()) {
            $this->registerNavItem();
            $this->registerCpRoutes();
        }

        \Craft::info('Loaded themes plugin', __METHOD__);
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
     * Redirects settings request to custom page
     * 
     * @return Response
     */
    public function getSettingsResponse ()
    {
        \Craft::$app->controller->redirect(
            UrlHelper::cpUrl('themes/settings')
        );
    }

    /**
     * Registers plugins related events
     */
    protected function registerPluginsEvents()
    {
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

        $_this = $this;
        Event::on(Plugins::class, Plugins::EVENT_BEFORE_INSTALL_PLUGIN,
            function (PluginEvent $event) use ($_this) {
                $_this->installDependency($event->plugin);
            }
        );
    }

    /**
     * Register cp routes
     */
    protected function registerCpRoutes()
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            if (\Craft::$app->config->getGeneral()->allowAdminChanges) {
                $event->rules = array_merge($event->rules, [
                    'themes/settings' => 'themes/cp-settings',
                    'themes' => 'themes/cp-themes',
                    'themes/layouts' => 'themes/cp-layouts',
                    'themes/layouts/<themeName:[\w-]+>' => 'themes/cp-layouts',
                    'themes/layouts/<themeName:[\w-]+>/<layout:\d+>' => 'themes/cp-layouts',
                    'themes/display' => 'themes/cp-display',
                    'themes/ajax/fields/<layout:\d+>' => 'themes/cp-fields',
                    'themes/ajax/fields/save' => 'themes/cp-fields/save',
                    'themes/ajax/view-modes/<layout:\d+>' => 'themes/cp-display/view-modes',
                    'themes/ajax/layouts/save' => 'themes/cp-layouts/save',
                    'themes/ajax/layouts/delete/<id:\d+>' => 'themes/cp-layouts/delete',
                    'themes/ajax/blocks/<layout:\d+>' => 'themes/cp-blocks/blocks',
                    'themes/ajax/providers' => 'themes/cp-blocks/providers',
                    'themes/ajax/field-options' => 'themes/cp-fields/options'
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
            'blocks' => BlockService::class,
            'viewModes' => ViewModeService::class,
            'fields' => FieldsService::class,
        ]);
    }

    /**
     * Registers the system block provider
     */
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
                    ThemesRules::clearCaches();
                }
            ];
        });
    }

    /**
     * Listens to Entries/Categories/Routes deletions and additions
     */
    protected function registerElementsEvents()
    {
        Craft::$app->projectConfig
            ->onRemove(Sections::CONFIG_ENTRYTYPES_KEY.'.{uid}',     [$this->layouts, 'onElementDeleted'])
            ->onAdd(Sections::CONFIG_ENTRYTYPES_KEY.'.{uid}',        [$this->layouts, 'onEntryTypeAdded'])
            ->onRemove(Routes::CONFIG_ROUTES_KEY.'.{uid}',     [$this->layouts, 'onElementDeleted'])
            ->onAdd(Routes::CONFIG_ROUTES_KEY.'.{uid}',        [$this->layouts, 'onRouteAdded'])
            ->onRemove(Categories::CONFIG_CATEGORYROUP_KEY.'.{uid}', [$this->layouts, 'onElementDeleted'])
            ->onAdd(Categories::CONFIG_CATEGORYROUP_KEY.'.{uid}',    [$this->layouts, 'onCategoryAdded']);
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
                        'url' => 'themes',
                        'label' => \Craft::t('themes', 'Theming'),
                        'subnav' => [
                            'themes' => [
                                'url' => 'themes',
                                'label' => \Craft::t('themes', 'Themes'),
                            ],
                            'themes-layouts' => [
                                'url' => 'themes/layouts',
                                'label' => \Craft::t('themes', 'Layouts'),
                            ],
                            'themes-display' => [
                                'url' => 'themes/display',
                                'label' => \Craft::t('themes', 'Display'),
                            ],
                            'themes-settings' => [
                                'url' => 'themes/settings',
                                'label' => \Craft::t('themes', 'Settings'),
                            ]
                        ]
                    ];
                }
            });
        }
    }

    /**
     * Registers project config events
     */
    protected function registerProjectConfig()
    {
        Craft::$app->projectConfig
            ->onAdd(BlockService::CONFIG_KEY.'.{uid}',       [$this->blocks,    'handleChanged'])
            ->onUpdate(BlockService::CONFIG_KEY.'.{uid}',    [$this->blocks,    'handleChanged'])
            ->onRemove(BlockService::CONFIG_KEY.'.{uid}',    [$this->blocks,    'handleDeleted'])
            ->onAdd(LayoutService::CONFIG_KEY.'.{uid}',      [$this->layouts,   'handleChanged'])
            ->onUpdate(LayoutService::CONFIG_KEY.'.{uid}',   [$this->layouts,   'handleChanged'])
            ->onRemove(LayoutService::CONFIG_KEY.'.{uid}',   [$this->layouts,   'handleDeleted'])
            ->onAdd(ViewModeService::CONFIG_KEY.'.{uid}',    [$this->viewModes, 'handleChanged'])
            ->onUpdate(ViewModeService::CONFIG_KEY.'.{uid}', [$this->viewModes, 'handleChanged'])
            ->onRemove(ViewModeService::CONFIG_KEY.'.{uid}', [$this->viewModes, 'handleDeleted'])
            ->onAdd(FieldsService::CONFIG_KEY.'.{uid}',      [$this->fields,    'handleChanged'])
            ->onUpdate(FieldsService::CONFIG_KEY.'.{uid}',   [$this->fields,    'handleChanged'])
            ->onRemove(FieldsService::CONFIG_KEY.'.{uid}',   [$this->fields,    'handleDeleted']);

        Event::on(ProjectConfig::class, ProjectConfig::EVENT_REBUILD, function(RebuildConfigEvent $e) {
            Themes::$plugin->blocks->rebuildLayoutConfig($e);
            Themes::$plugin->layouts->rebuildLayoutConfig($e);
            Themes::$plugin->viewModes->rebuildLayoutConfig($e);
            Themes::$plugin->fields->rebuildLayoutConfig($e);
        });
    }

    /**
     * Resolves current theme and registers aliases, templates & hooks
     */
    protected function resolveTheme(RegisterTemplateRootsEvent $event)
    {
        \Craft::info('Resolving current theme', __METHOD__);
        $theme = $this->rules->resolveCurrentTheme();
        $this->registry->setCurrent($theme);

        if (!$theme) {
            \Craft::info("No theme found for request ".\Craft::$app->request->getUrl(), __METHOD__);
            return null;
        }

        Event::on(View::class, View::EVENT_BEFORE_RENDER_PAGE_TEMPLATE, function(TemplateEvent $event) use ($theme) {
            $event->variables['layout'] = $theme->getLayout();
        });

        \Yii::setAlias('@themePath', '@root/themes/' . $theme->handle);
        \Yii::setAlias('@themeWebPath', '@webroot/themes/' . $theme->handle);
        Craft::$app->view->registerTwigExtension(new TwigTheme);
        $event->roots[''][] = __DIR__ . '/templates/front';
        $event->roots[''] = array_merge($theme->getTemplatePaths(), $event->roots['']);
        $path = \Craft::$app->request->getPathInfo();
        $theme->registerAssetBundles($path);
        $theme->afterSet();
        \Craft::info("Theme has been set to : " . $theme->name, __METHOD__);
    }

    /**
     * Install parent theme
     * 
     * @param PluginInterface $plugin
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
     * @inheritdoc
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function afterInstall()
    {
        $this->layouts->createAll();
    }
}
