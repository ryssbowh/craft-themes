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
use Ryssbowh\CraftThemes\services\{BlockProvidersService, BlockService, FieldDisplayerService, LayoutService, FieldsService, RulesService, ViewModeService, ViewService, ThemesRegistry, CacheService, DisplayService, GroupService, MatrixService};
use Ryssbowh\CraftThemes\twig\ThemesVariable;
use Ryssbowh\CraftThemes\twig\TwigTheme;
use craft\base\PluginInterface;
use craft\events\{CategoryGroupEvent, ConfigEvent, EntryTypeEvent, FieldEvent, GlobalSetEvent, RegisterUserPermissionsEvent, RouteEvent, TagGroupEvent, VolumeEvent, PluginEvent, RebuildConfigEvent, RegisterCacheOptionsEvent, RegisterCpNavItemsEvent, RegisterTemplateRootsEvent, RegisterUrlRulesEvent, TemplateEvent};
use craft\helpers\UrlHelper;
use craft\services\{Categories, Plugins, ProjectConfig, Routes, Sections, Volumes, UserPermissions, Tags, Globals, Fields};
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

    public $hasCpSection = true;

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
        $this->registerPermissions();
        $this->registerClearCacheEvent();
        $this->registerBlockProviders();
        $this->registerProjectConfig();
        $this->registerPluginsEvents();
        $this->registerCraftEvents();
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
            $this->registerCpRoutes();
        }

        \Craft::info('Loaded themes plugin', __METHOD__);
    }

    public function getCpNavItem()
    {
        $item = [
            'url' => 'themes',
            'label' => \Craft::t('themes', 'Theming'),
            'subnav' => [
                'themes' => [
                    'url' => 'themes',
                    'label' => \Craft::t('themes', 'Themes'),
                ]
            ]
        ];
        $user = \Craft::$app->user;
        if ($user->checkPermission('manageThemesBlocks')) {
            $item['subnav']['themes-blocks'] = [
                'url' => 'themes/blocks',
                'label' => \Craft::t('themes', 'Blocks'),
            ];
        }
        if ($user->checkPermission('manageThemesDisplay')) {
            $item['subnav']['themes-display'] = [
                'url' => 'themes/display',
                'label' => \Craft::t('themes', 'Display'),
            ];
        }
        if ($user->checkPermission('manageThemesRules')) {
            $item['subnav']['themes-rules'] = [
                'url' => 'themes/rules',
                'label' => \Craft::t('themes', 'Rules'),
            ];
        }
        return $item;
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

    protected function settingsHtml(): string
    {
        \Craft::$app->view->registerAssetBundle(SettingsAssets::class);
        return Craft::$app->view->renderTemplate(
            'themes/cp/settings',
            [
                'settings' => $this->getSettings()
            ]
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
                    'themes/rules' => 'themes/cp-rules',
                    'themes/save-rules' => 'themes/cp-rules/save',
                    'themes/blocks' => 'themes/cp-blocks',
                    'themes/blocks/<themeName:[\w-]+>' => 'themes/cp-blocks',
                    'themes/blocks/<themeName:[\w-]+>/<layout:\d+>' => 'themes/cp-blocks',
                    'themes/display' => 'themes/cp-display',
                    'themes/display/<themeName:[\w-]+>' => 'themes/cp-display',
                    'themes/display/<themeName:[\w-]+>/<layout:\d+>' => 'themes/cp-display',

                    'themes/ajax/displays/<layout:\d+>' => 'themes/cp-ajax/displays',
                    'themes/ajax/displays/save' => 'themes/cp-ajax/save-displays',
                    'themes/ajax/view-modes/<layout:\d+>' => 'themes/cp-ajax/view-modes',
                    'themes/ajax/blocks/save' => 'themes/cp-ajax/save-blocks',
                    'themes/ajax/layouts/delete/<id:\d+>' => 'themes/cp-ajax/delete-layout',
                    'themes/ajax/blocks/<layout:\d+>' => 'themes/cp-ajax/blocks',
                    'themes/ajax/block-providers' => 'themes/cp-ajax/block-providers',
                    'themes/ajax/field-options' => 'themes/cp-ajax/field-options',
                    'themes/ajax/field-options/validate' => 'themes/cp-ajax/validate-field-options',
                    'themes/ajax/install' => 'themes/cp-ajax/install',

                    'themes/test' => 'themes/cp-install/test'
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
                'class' => RulesService::class,
                'rules' => $this->getSettings()->getRules(),
                'default' => $this->getSettings()->default
            ],
            'layouts' => LayoutService::class,
            'blockProviders' => BlockProvidersService::class,
            'blocks' => BlockService::class,
            'viewModes' => ViewModeService::class,
            'fieldDisplayers' => FieldDisplayerService::class,
            'view' => [
                'class' => ViewService::class,
                'devMode' => $this->getSettings()->devModeEnabled,
                'eagerLoad' => $this->getSettings()->eagerLoad
            ],
            'cache' => CacheService::class,
            'display' => DisplayService::class,
            'fields' => FieldsService::class
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
     * Listens to Entries/Categories/Routes/Fields deletions and additions
     */
    protected function registerCraftEvents()
    {
        Event::on(Sections::class, Sections::EVENT_AFTER_SAVE_ENTRY_TYPE, function (EntryTypeEvent $e) {
            $type = LayoutService::ENTRY_HANDLE;
            $uid = $e->entryType->uid;
            Themes::$plugin->layouts->onCraftElementSaved($type, $uid);
            Themes::$plugin->display->onCraftElementSaved($type, $uid);
        });
        Event::on(Sections::class, Sections::EVENT_AFTER_DELETE_ENTRY_TYPE, function (EntryTypeEvent $e) {
            $type = LayoutService::ENTRY_HANDLE;
            $uid = $e->entryType->uid;
            Themes::$plugin->layouts->onCraftElementDeleted($type, $uid);
        });
        Event::on(Categories::class, Categories::EVENT_AFTER_SAVE_GROUP, function (CategoryGroupEvent $e) {
            $type = LayoutService::CATEGORY_HANDLE;
            $uid = $e->categoryGroup->uid;
            Themes::$plugin->layouts->onCraftElementSaved($type, $uid);
            Themes::$plugin->display->onCraftElementSaved($type, $uid);
        });
        Event::on(Categories::class, Categories::EVENT_AFTER_DELETE_GROUP, function (CategoryGroupEvent $e) {
            $type = LayoutService::CATEGORY_HANDLE;
            $uid = $e->categoryGroup->uid;
            Themes::$plugin->layouts->onCraftElementDeleted($type, $uid);
        });
        Event::on(Volumes::class, Volumes::EVENT_AFTER_SAVE_VOLUME, function (VolumeEvent $e) {
            $type = LayoutService::VOLUME_HANDLE;
            $uid = $e->volume->uid;
            Themes::$plugin->layouts->onCraftElementSaved($type, $uid);
            Themes::$plugin->display->onCraftElementSaved($type, $uid);
        });
        Event::on(Volumes::class, Volumes::EVENT_AFTER_DELETE_VOLUME, function (VolumeEvent $e) {
            $type = LayoutService::VOLUME_HANDLE;
            $uid = $e->volume->uid;
            Themes::$plugin->layouts->onCraftElementDeleted($type, $uid);
        });
        Event::on(Globals::class, Globals::EVENT_AFTER_SAVE_GLOBAL_SET, function (GlobalSetEvent $e) {
            $type = LayoutService::GLOBAL_HANDLE;
            $uid = $e->globalSet->uid;
            Themes::$plugin->layouts->onCraftElementSaved($type, $uid);
            Themes::$plugin->display->onCraftElementSaved($type, $uid);
        });
        Craft::$app->projectConfig->onRemove(Globals::CONFIG_GLOBALSETS_KEY.'.{uid}', function(ConfigEvent $e) {
            $type = LayoutService::GLOBAL_HANDLE;
            $uid = $e->tokenMatches[0];
            Themes::$plugin->layouts->onCraftElementDeleted($type, $uid);
        });
        Event::on(Tags::class, Tags::EVENT_AFTER_SAVE_GROUP, function (TagGroupEvent $e) {
            $type = LayoutService::TAG_HANDLE;
            $uid = $e->tagGroup->uid;
            Themes::$plugin->layouts->onCraftElementSaved($type, $uid);
            Themes::$plugin->display->onCraftElementSaved($type, $uid);
        });
        Event::on(Tags::class, Tags::EVENT_AFTER_DELETE_GROUP, function (TagGroupEvent $e) {
            $type = LayoutService::TAG_HANDLE;
            $uid = $e->tagGroup->uid;
            Themes::$plugin->layouts->onCraftElementDeleted($type, $uid);
        });
        Craft::$app->projectConfig->onRemove(Routes::CONFIG_ROUTES_KEY.'.{uid}', function(ConfigEvent $e) {
            $type = LayoutService::ROUTE_HANDLE;
            $uid = $e->tokenMatches[0];
            Themes::$plugin->layouts->onCraftElementDeleted($type, $uid);
        })->onAdd(Routes::CONFIG_ROUTES_KEY.'.{uid}', function(ConfigEvent $e) {
            $type = LayoutService::ROUTE_HANDLE;
            $uid = $e->tokenMatches[0];
            Themes::$plugin->layouts->onCraftElementSaved($type, $uid);
        })->onUpdate(Routes::CONFIG_ROUTES_KEY.'.{uid}', function(ConfigEvent $e) {
            $type = LayoutService::ROUTE_HANDLE;
            $uid = $e->tokenMatches[0];
            Themes::$plugin->layouts->onCraftElementSaved($type, $uid);
        });
        Event::on(Fields::class, Fields::EVENT_AFTER_SAVE_FIELD, function (FieldEvent $e) {
            Themes::$plugin->display->onCraftFieldSaved($e->field);
        });
        Event::on(Fields::class, Fields::EVENT_AFTER_DELETE_FIELD, function (FieldEvent $e) {
            Themes::$plugin->display->onCraftFieldDeleted($e->field);
        });
    }

    /**
     * Registers project config events
     */
    protected function registerProjectConfig()
    {
        Craft::$app->projectConfig
            ->onAdd(LayoutService::CONFIG_KEY.'.{uid}',      [$this->layouts, 'handleChanged'])
            ->onUpdate(LayoutService::CONFIG_KEY.'.{uid}',   [$this->layouts, 'handleChanged'])
            ->onRemove(LayoutService::CONFIG_KEY.'.{uid}',   [$this->layouts, 'handleDeleted'])
            ->onAdd(DisplayService::CONFIG_KEY.'.{uid}',     [$this->display, 'handleChanged'])
            ->onUpdate(DisplayService::CONFIG_KEY.'.{uid}',  [$this->display, 'handleChanged'])
            ->onRemove(DisplayService::CONFIG_KEY.'.{uid}',  [$this->display, 'handleDeleted']);

        Event::on(ProjectConfig::class, ProjectConfig::EVENT_REBUILD, function(RebuildConfigEvent $e) {
            // Themes::$plugin->blocks->rebuildLayoutConfig($e);
            // Themes::$plugin->layouts->rebuildLayoutConfig($e);
            // Themes::$plugin->viewModes->rebuildLayoutConfig($e);
            // Themes::$plugin->display->rebuildLayoutConfig($e);
        });
    }

    protected function registerPermissions()
    {
        if (\Craft::$app->getEdition() !== \Craft::Solo) {
            Event::on(
                UserPermissions::class,
                UserPermissions::EVENT_REGISTER_PERMISSIONS,
                function (RegisterUserPermissionsEvent $event) {
                    $event->permissions['themes'] = [
                        'manageThemesBlocks' => [
                            'label' => \Craft::t('themes', 'Manage blocks')
                        ],
                        'manageThemesDisplay' => [
                            'label' => \Craft::t('themes', 'Manage display')
                        ],
                        'manageThemesRules' => [
                            'label' => \Craft::t('themes', 'Manage rules')
                        ]
                    ];
                }
            );
        }
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

    protected function afterUninstall()
    {
        \Craft::$app->getProjectConfig()->remove('themes');
    }
}
