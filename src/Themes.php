<?php
namespace Ryssbowh\CraftThemes;

use Craft;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\assets\SettingsAssets;
use Ryssbowh\CraftThemes\events\FieldDisplayerEvent;
use Ryssbowh\CraftThemes\events\RegisterBlockProvidersEvent;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\Settings;
use Ryssbowh\CraftThemes\models\SystemBlockProvider;
use Ryssbowh\CraftThemes\services\{BlockProvidersService, BlockService, FieldDisplayerService, FieldsService, LayoutService, ThemesRules, ViewModeService, ViewService, ThemesRegistry, CacheService, DisplayService, GroupService, MatrixService};
use Ryssbowh\CraftThemes\twig\ThemesVariable;
use Ryssbowh\CraftThemes\twig\TwigTheme;
use craft\base\PluginInterface;
use craft\events\{PluginEvent, RebuildConfigEvent, RegisterCacheOptionsEvent, RegisterCpNavItemsEvent, RegisterTemplateRootsEvent, RegisterUrlRulesEvent, TemplateEvent};
use craft\helpers\UrlHelper;
use craft\services\Fields;
use craft\services\{Categories, Plugins, ProjectConfig, Routes, Sections};
use craft\utilities\ClearCaches;
use craft\web\Application;
use craft\web\UrlManager;
use craft\web\View;
use craft\web\twig\variables\Cp;
use craft\web\twig\variables\CraftVariable;
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

        \Yii::setAlias('@themesPath', '@root/themes');
        \Yii::setAlias('@themesWebPath', '@webroot/themes');

        $this->registerServices();
        $this->registerClearCacheEvent();
        $this->registerBlockProviders();
        $this->registerProjectConfig();
        $this->registerPluginsEvents();
        $this->registerCraftConfigEvents();
        $this->registerTwigVariables();

        if (Craft::$app->request->getIsSiteRequest()) {
            Event::on(
                Application::class, 
                Application::EVENT_BEFORE_REQUEST, 
                [$this->rules, 'resolveCurrentTheme']
            );
            Event::on(
                View::class, 
                View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
                [$this->registry, 'registerCurrentThemeTemplates']
            );
            Event::on(
                View::class, 
                View::EVENT_BEFORE_RENDER_PAGE_TEMPLATE,
                [$this->view, 'beforeRenderPage']
            );
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
        $this->cache->flush();
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

    protected function registerTwigVariables()
    {
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                $variable = $event->sender;
                $variable->set('themes', ThemesVariable::class);
            }
        );
    }

    /**
     * Registers plugins related events
     */
    protected function registerPluginsEvents()
    {
        $_this = $this;

        Event::on(Plugins::class, Plugins::EVENT_AFTER_ENABLE_PLUGIN,
            function (PluginEvent $event) use ($_this) {
                $_this->cache->flush();
            }
        );

        Event::on(Plugins::class, Plugins::EVENT_AFTER_DISABLE_PLUGIN,
            function (PluginEvent $event) use ($_this) {
                $_this->cache->flush();
            }
        );

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
                    'themes' => 'themes/cp-themes',
                    'themes/settings' => 'themes/cp-settings',
                    'themes/layouts' => 'themes/cp-layouts',
                    'themes/layouts/<themeName:[\w-]+>' => 'themes/cp-layouts',
                    'themes/layouts/<themeName:[\w-]+>/<layout:\d+>' => 'themes/cp-layouts',
                    'themes/display' => 'themes/cp-display',
                    'themes/display/<themeName:[\w-]+>' => 'themes/cp-display',
                    'themes/display/<themeName:[\w-]+>/<layout:\d+>' => 'themes/cp-display',

                    'themes/ajax/displays/<layout:\d+>' => 'themes/cp-ajax/displays',
                    'themes/ajax/displays/save' => 'themes/cp-ajax/save-displays',
                    'themes/ajax/view-modes/<layout:\d+>' => 'themes/cp-ajax/view-modes',
                    'themes/ajax/layouts/save' => 'themes/cp-ajax/save-layout',
                    'themes/ajax/layouts/delete/<id:\d+>' => 'themes/cp-ajax/delete-layout',
                    'themes/ajax/blocks/<layout:\d+>' => 'themes/cp-ajax/blocks',
                    'themes/ajax/providers' => 'themes/cp-ajax/providers',
                    'themes/ajax/display-options' => 'themes/cp-ajax/display-options',
                    'themes/ajax/display-options/validate' => 'themes/cp-ajax/validate-display-options',
                    'themes/ajax/repair' => 'themes/cp-ajax/repair',

                    'themes/test' => 'themes/cp-repair/test'
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
            'fieldDisplayers' => FieldDisplayerService::class,
            'view' => [
                'class' => ViewService::class,
                'devMode' => $this->getSettings()->devModeEnabled,
                'eagerLoad' => $this->getSettings()->eagerLoad
            ],
            'cache' => CacheService::class,
            'display' => [
                'class' => DisplayService::class,
                'memoryLoading' => $this->getSettings()->memoryLoading
            ]
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
                    Themes::$plugin->cache->flush();
                }
            ];
        });
    }

    /**
     * Listens to Entries/Categories/Routes deletions and additions
     */
    protected function registerCraftConfigEvents()
    {
        Craft::$app->projectConfig
            ->onRemove(Sections::CONFIG_ENTRYTYPES_KEY.'.{uid}',     [$this->layouts, 'onCraftElementDeleted'])
            ->onAdd(Sections::CONFIG_ENTRYTYPES_KEY.'.{uid}',        [$this->layouts, 'onEntryTypeAdded'])
            ->onAdd(Sections::CONFIG_ENTRYTYPES_KEY.'.{uid}',        [$this->display, 'onCraftElementChanged'])
            ->onUpdate(Sections::CONFIG_ENTRYTYPES_KEY.'.{uid}',     [$this->display, 'onCraftElementChanged'])
            ->onRemove(Routes::CONFIG_ROUTES_KEY.'.{uid}',           [$this->layouts, 'onCraftElementDeleted'])
            ->onAdd(Routes::CONFIG_ROUTES_KEY.'.{uid}',              [$this->layouts, 'onRouteAdded'])
            ->onUpdate(Routes::CONFIG_ROUTES_KEY.'.{uid}',           [$this->layouts, 'onRouteUpdated'])
            ->onRemove(Categories::CONFIG_CATEGORYROUP_KEY.'.{uid}', [$this->layouts, 'onCraftElementDeleted'])
            ->onAdd(Categories::CONFIG_CATEGORYROUP_KEY.'.{uid}',    [$this->layouts, 'onCategoryAdded'])
            ->onAdd(Categories::CONFIG_CATEGORYROUP_KEY.'.{uid}',    [$this->display, 'onCraftElementChanged'])
            ->onUpdate(Categories::CONFIG_CATEGORYROUP_KEY.'.{uid}', [$this->display, 'onCraftElementChanged'])
            ->onRemove(Fields::CONFIG_FIELDS_KEY.'.{uid}',           [$this->display, 'onCraftFieldDeleted']);
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
            ->onAdd(DisplayService::CONFIG_KEY.'.{uid}',     [$this->display,   'handleChanged'])
            ->onUpdate(DisplayService::CONFIG_KEY.'.{uid}',  [$this->display,   'handleChanged'])
            ->onRemove(DisplayService::CONFIG_KEY.'.{uid}',  [$this->display,   'handleDeleted'])

            ->onAdd(FieldsService::CONFIG_KEY.'.{uid}',      [$this->fields,    'handleChanged'])
            ->onUpdate(FieldsService::CONFIG_KEY.'.{uid}',   [$this->fields,    'handleChanged']);

        Event::on(ProjectConfig::class, ProjectConfig::EVENT_REBUILD, function(RebuildConfigEvent $e) {
            Themes::$plugin->blocks->rebuildLayoutConfig($e);
            Themes::$plugin->layouts->rebuildLayoutConfig($e);
            Themes::$plugin->viewModes->rebuildLayoutConfig($e);
            Themes::$plugin->fields->rebuildLayoutConfig($e);
        });
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
        $this->display->createAll();
    }

    protected function afterUninstall()
    {
        $projectConfig = \Craft::$app->getProjectConfig();
        $projectConfig->remove('themes');
    }
}
