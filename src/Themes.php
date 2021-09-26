<?php
namespace Ryssbowh\CraftThemes;

use Craft;
use Detection\MobileDetect;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\assets\SettingsAssets;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\Settings;
use Ryssbowh\CraftThemes\services\{BlockProvidersService, BlockService, FieldDisplayerService, LayoutService, FieldsService, RulesService, ViewModeService, ViewService, ThemesRegistry, CacheService, DisplayService, GroupService, MatrixService, TablesService, FileDisplayerService, BlockCacheService, GroupsService};
use Ryssbowh\CraftThemes\twig\ThemesVariable;
use Ryssbowh\CraftThemes\twig\TwigTheme;
use craft\base\PluginInterface;
use craft\events\{CategoryGroupEvent, ConfigEvent, EntryTypeEvent, FieldEvent, GlobalSetEvent, RegisterUserPermissionsEvent, TagGroupEvent, VolumeEvent, PluginEvent, RebuildConfigEvent, RegisterCacheOptionsEvent, RegisterCpNavItemsEvent, RegisterTemplateRootsEvent, RegisterUrlRulesEvent, TemplateEvent};
use craft\services\{Categories, Plugins, ProjectConfig, Sections, Volumes, UserPermissions, Tags, Globals, Fields, Users};
use craft\utilities\ClearCaches;
use craft\web\UrlManager;
use craft\web\View;
use craft\web\twig\variables\Cp;
use craft\web\twig\variables\CraftVariable;
use yii\base\Application;
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
    public $schemaVersion = '3.0.0';
    
    /**
     * @inheritdoc
     */
    public $hasCpSettings = true;

    /**
     * @inheritdoc
     */
    public $hasCpSection = true;

    /**
     * inheritDoc
     */
    public function init()
    {
        parent::init();

        self::$plugin = $this;

        \Yii::setAlias('@themesWebPath', '@webroot/themes');
        \Yii::setAlias('@themesWeb', '@web/themes');

        $this->registerServices();
        $this->registerPermissions();
        $this->registerClearCacheEvent();
        $this->registerProjectConfig();
        $this->registerPluginsEvents();
        $this->registerCraftEvents();
        $this->registerTwigVariables();

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

        if (Craft::$app->request->getIsCpRequest()) {
            $this->registerCpRoutes();
        }

        \Craft::info('Loaded themes plugin', __METHOD__);
    }

    /**
     * @inheritDoc
     */
    public function getCpNavItem()
    {
        $item = [
            'url' => 'themes',
            'label' => \Craft::t('themes', 'Theming'),
            'subnav' => [
                'themes' => [
                    'url' => 'themes/list',
                    'label' => \Craft::t('themes', 'Themes'),
                ]
            ]
        ];
        if (\Craft::$app->config->getGeneral()->allowAdminChanges) {
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
        }
        return $item;
    }

    /**
     * Registers twig variables
     */
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
     * @inheritDoc
     */
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
        Event::on(Plugins::class, Plugins::EVENT_BEFORE_DISABLE_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin->handle == 'themes') {
                    Themes::$app->registry->disableAll();
                }
                if ($event->plugin instanceof ThemeInterface) {
                    $deps = Themes::$plugin->registry->getDependencies($event->plugin);
                    foreach ($deps as $theme) {
                        \Craft::$app->plugins->disablePlugin($theme->handle);
                    }
                }
            }
        );

        Event::on(Plugins::class, Plugins::EVENT_AFTER_DISABLE_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin instanceof ThemeInterface) {
                    Themes::$plugin->registry->resetThemes();
                    Themes::$plugin->rules->flushCache();
                }
            }
        );

        Event::on(Plugins::class, Plugins::EVENT_BEFORE_ENABLE_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin instanceof ThemeInterface) {
                    $extends = $event->plugin->extends;
                    if ($extends) {
                        \Craft::$app->plugins->enablePlugin($extends);
                    }
                }
            }
        );

        Event::on(Plugins::class, Plugins::EVENT_AFTER_ENABLE_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin instanceof ThemeInterface) {
                    Themes::$plugin->registry->resetThemes();
                    Themes::$plugin->rules->flushCache();
                }
            }
        );

        Event::on(Plugins::class, Plugins::EVENT_BEFORE_UNINSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin instanceof ThemeInterface) {
                    $deps = Themes::$plugin->registry->getDependencies($event->plugin);
                    foreach ($deps as $theme) {
                        \Craft::$app->plugins->uninstallPlugin($theme->handle);
                    }
                    Themes::$plugin->registry->uninstallTheme($event->plugin);
                }
            }
        );

        Event::on(Plugins::class, Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin instanceof ThemeInterface) {
                    $extends = $event->plugin->extends;
                    if ($extends) {
                        \Craft::$app->plugins->installPlugin($extends);
                    }
                    Themes::$plugin->registry->installTheme($event->plugin);
                }
            }
        );
    }

    /**
     * Register cp routes
     */
    protected function registerCpRoutes()
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules = array_merge($event->rules, [
                'themes' => 'themes/cp-themes',
                'themes/list' => 'themes/cp-themes/list',
            ]);
            if (\Craft::$app->config->getGeneral()->allowAdminChanges) {
                $event->rules = array_merge($event->rules, [
                    'themes/rules' => 'themes/cp-rules',
                    'themes/save-rules' => 'themes/cp-rules/save',
                    'themes/blocks' => 'themes/cp-blocks',
                    'themes/blocks/<themeName:[\w-]+>' => 'themes/cp-blocks',
                    'themes/blocks/<themeName:[\w-]+>/<layout:\d+>' => 'themes/cp-blocks',
                    'themes/display' => 'themes/cp-display',
                    'themes/display/<themeName:[\w-]+>' => 'themes/cp-display',
                    'themes/display/<themeName:[\w-]+>/<layout:\d+>' => 'themes/cp-display',

                    'themes/ajax/blocks/save' => 'themes/cp-blocks-ajax/save-blocks',
                    'themes/ajax/layouts/delete/<id:\d+>' => 'themes/cp-blocks-ajax/delete-layout',
                    'themes/ajax/blocks/<layout:\d+>' => 'themes/cp-blocks-ajax/blocks',
                    'themes/ajax/block-providers' => 'themes/cp-blocks-ajax/block-providers',

                    'themes/ajax/validate-field-options' => 'themes/cp-display-ajax/validate-field-options',

                    'themes/ajax/install' => 'themes/cp-ajax/install',
                    'themes/ajax/entries/<uid:[\w-]+>' => 'themes/cp-ajax/entries',
                    'themes/ajax/categories/<uid:[\w-]+>' => 'themes/cp-ajax/categories',
                    'themes/ajax/users' => 'themes/cp-ajax/users',

                    'themes/ajax/view-modes/<theme:[\w-]+>/<type:[\w]+>/<uid:[\w-]+>' => 'themes/cp-view-modes-ajax/view-modes',
                    'themes/ajax/view-modes/<theme:[\w-]+>/<type:[\w]+>' => 'themes/cp-view-modes-ajax/view-modes',
                    'themes/ajax/view-modes/save' => 'themes/cp-view-modes-ajax/save',
                    'themes/ajax/view-modes' => 'themes/cp-view-modes-ajax/get'
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
                'default' => $this->getSettings()->default,
                'cache' => \Craft::$app->cache,
                'cacheEnabled' => $this->getSettings()->rulesCacheEnabled,
                'console' => $this->getSettings()->console,
                'setConsole' => $this->getSettings()->setConsole,
                'cp' => $this->getSettings()->cp,
                'setCp' => $this->getSettings()->setCp,
                'mobileDetect' => new MobileDetect()
            ],
            'layouts' => LayoutService::class,
            'blockProviders' => BlockProvidersService::class,
            'blocks' => BlockService::class,
            'viewModes' => ViewModeService::class,
            'fieldDisplayers' => FieldDisplayerService::class,
            'view' => [
                'class' => ViewService::class,
                'cache' => \Craft::$app->cache,
                'devMode' => $this->getSettings()->devModeEnabled,
                'eagerLoad' => $this->getSettings()->eagerLoad,
                'templateCacheEnabled' => $this->getSettings()->templateCacheEnabled
            ],
            'blockCache' => [
                'class' => BlockCacheService::class,
                'cache' => \Craft::$app->cache,
                'cacheEnabled' => $this->getSettings()->blockCacheEnabled
            ],
            'displays' => DisplayService::class,
            'fields' => FieldsService::class,
            'matrix' => MatrixService::class,
            'tables' => TablesService::class,
            'fileDisplayers' => FileDisplayerService::class,
            'groups' => GroupsService::class,
        ]);
    }

    /**
     * Clear cache event subscription
     */
    protected function registerClearCacheEvent()
    {
        Event::on(ClearCaches::class, ClearCaches::EVENT_REGISTER_CACHE_OPTIONS, function (RegisterCacheOptionsEvent $event) {
            $event->options[] = [
                'key' => 'themes-template-cache',
                'label' => Craft::t('themes', 'Themes templates'),
                'action' => function() {
                    Themes::$plugin->view->flushTemplateCache();
                }
            ];
            $event->options[] = [
                'key' => 'themes-rules-cache',
                'label' => Craft::t('themes', 'Themes rules'),
                'action' => function() {
                    Themes::$plugin->rules->flushCache();
                }
            ];
            $event->options[] = [
                'key' => 'themes-block-cache',
                'label' => Craft::t('themes', 'Themes blocks'),
                'action' => function() {
                    Themes::$plugin->blockCache->flush();
                }
            ];
        });
    }

    /**
     * Listens to Entries/Categories/Volumes/Tags/Globals/Fields deletions and additions
     */
    protected function registerCraftEvents()
    {
        $layouts = $this->layouts;
        Craft::$app->projectConfig
            ->onAdd(Sections::CONFIG_ENTRYTYPES_KEY.'.{uid}', function (ConfigEvent $e) use ($layouts) {
                $layouts->onCraftElementSaved(LayoutService::ENTRY_HANDLE, $e->tokenMatches[0]);
            })
            ->onUpdate(Sections::CONFIG_ENTRYTYPES_KEY.'.{uid}', function (ConfigEvent $e) use ($layouts) {
                $layouts->onCraftElementSaved(LayoutService::ENTRY_HANDLE, $e->tokenMatches[0]);
            })
            ->onRemove(Sections::CONFIG_ENTRYTYPES_KEY.'.{uid}', function (ConfigEvent $e) use ($layouts) {
                $layouts->onCraftElementDeleted($e->tokenMatches[0]);
            })
            ->onAdd(Categories::CONFIG_CATEGORYROUP_KEY.'.{uid}', function (ConfigEvent $e) use ($layouts) {
                $layouts->onCraftElementSaved(LayoutService::CATEGORY_HANDLE, $e->tokenMatches[0]);
            })
            ->onUpdate(Categories::CONFIG_CATEGORYROUP_KEY.'.{uid}', function (ConfigEvent $e) use ($layouts) {
                $layouts->onCraftElementSaved(LayoutService::CATEGORY_HANDLE, $e->tokenMatches[0]);
            })
            ->onRemove(Categories::CONFIG_CATEGORYROUP_KEY.'.{uid}', function (ConfigEvent $e) use ($layouts) {
                $layouts->onCraftElementDeleted($e->tokenMatches[0]);
            })
            ->onAdd(Volumes::CONFIG_VOLUME_KEY.'.{uid}', function (ConfigEvent $e) use ($layouts) {
                $layouts->onCraftElementSaved(LayoutService::VOLUME_HANDLE, $e->tokenMatches[0]);
            })
            ->onUpdate(Volumes::CONFIG_VOLUME_KEY.'.{uid}', function (ConfigEvent $e) use ($layouts) {
                $layouts->onCraftElementSaved(LayoutService::VOLUME_HANDLE, $e->tokenMatches[0]);
            })
            ->onRemove(Volumes::CONFIG_VOLUME_KEY.'.{uid}', function (ConfigEvent $e) use ($layouts) {
                $layouts->onCraftElementDeleted($e->tokenMatches[0]);
            })
            ->onAdd(Globals::CONFIG_GLOBALSETS_KEY.'.{uid}', function (ConfigEvent $e) use ($layouts) {
                $layouts->onCraftElementSaved(LayoutService::GLOBAL_HANDLE, $e->tokenMatches[0]);
            })
            ->onUpdate(Globals::CONFIG_GLOBALSETS_KEY.'.{uid}', function (ConfigEvent $e) use ($layouts) {
                $layouts->onCraftElementSaved(LayoutService::GLOBAL_HANDLE, $e->tokenMatches[0]);
            })
            ->onRemove(Globals::CONFIG_GLOBALSETS_KEY.'.{uid}', function (ConfigEvent $e) use ($layouts) {
                $layouts->onCraftElementDeleted($e->tokenMatches[0]);
            })
            ->onAdd(Tags::CONFIG_TAGGROUP_KEY.'.{uid}', function (ConfigEvent $e) use ($layouts) {
                $layouts->onCraftElementSaved(LayoutService::TAG_HANDLE, $e->tokenMatches[0]);
            })
            ->onUpdate(Tags::CONFIG_TAGGROUP_KEY.'.{uid}', function (ConfigEvent $e) use ($layouts) {
                $layouts->onCraftElementSaved(LayoutService::TAG_HANDLE, $e->tokenMatches[0]);
            })
            ->onRemove(Tags::CONFIG_TAGGROUP_KEY.'.{uid}', function (ConfigEvent $e) use ($layouts) {
                $layouts->onCraftElementDeleted($e->tokenMatches[0]);
            })
            ->onAdd(Users::CONFIG_USERLAYOUT_KEY, function (ConfigEvent $e) use ($layouts) {
                $layouts->onCraftElementSaved(LayoutService::USER_HANDLE);
            })
            ->onUpdate(Users::CONFIG_USERLAYOUT_KEY, function (ConfigEvent $e) use ($layouts) {
                $layouts->onCraftElementSaved(LayoutService::USER_HANDLE);
            });

        Event::on(Fields::class, Fields::EVENT_AFTER_SAVE_FIELD, function (FieldEvent $e) {
            Themes::$plugin->displays->onCraftFieldSaved($e);
        });
        // Event::on(Fields::class, Fields::EVENT_AFTER_DELETE_FIELD, function (FieldEvent $e) {
        //     Themes::$plugin->displays->onCraftFieldDeleted($e);
        // });
    }

    /**
     * Registers project config events
     */
    protected function registerProjectConfig()
    {
        Craft::$app->projectConfig
            ->onAdd(LayoutService::CONFIG_KEY.'.{uid}',      [$this->layouts,   'handleChanged'])
            ->onUpdate(LayoutService::CONFIG_KEY.'.{uid}',   [$this->layouts,   'handleChanged'])
            ->onRemove(LayoutService::CONFIG_KEY.'.{uid}',   [$this->layouts,   'handleDeleted'])
            ->onAdd(ViewModeService::CONFIG_KEY.'.{uid}',    [$this->viewModes, 'handleChanged'])
            ->onUpdate(ViewModeService::CONFIG_KEY.'.{uid}', [$this->viewModes, 'handleChanged'])
            ->onRemove(ViewModeService::CONFIG_KEY.'.{uid}', [$this->viewModes, 'handleDeleted'])
            ->onAdd(BlockService::CONFIG_KEY.'.{uid}',       [$this->blocks,    'handleChanged'])
            ->onUpdate(BlockService::CONFIG_KEY.'.{uid}',    [$this->blocks,    'handleChanged'])
            ->onRemove(BlockService::CONFIG_KEY.'.{uid}',    [$this->blocks,    'handleDeleted'])
            ->onAdd(DisplayService::CONFIG_KEY.'.{uid}',     [$this->displays,  'handleChanged'])
            ->onUpdate(DisplayService::CONFIG_KEY.'.{uid}',  [$this->displays,  'handleChanged'])
            ->onRemove(DisplayService::CONFIG_KEY.'.{uid}',  [$this->displays,  'handleDeleted'])
            ->onAdd(GroupsService::CONFIG_KEY.'.{uid}',      [$this->groups,    'handleChanged'])
            ->onUpdate(GroupsService::CONFIG_KEY.'.{uid}',   [$this->groups,    'handleChanged'])
            ->onRemove(GroupsService::CONFIG_KEY.'.{uid}',   [$this->groups,    'handleDeleted'])
            ->onAdd(FieldsService::CONFIG_KEY.'.{uid}',      [$this->fields,    'handleChanged'])
            ->onUpdate(FieldsService::CONFIG_KEY.'.{uid}',   [$this->fields,    'handleChanged'])
            ->onRemove(FieldsService::CONFIG_KEY.'.{uid}',   [$this->fields,    'handleDeleted']);

        Event::on(ProjectConfig::class, ProjectConfig::EVENT_REBUILD, function (RebuildConfigEvent $e) {
            Themes::$plugin->layouts->rebuildConfig($e);
            Themes::$plugin->viewModes->rebuildConfig($e);
            Themes::$plugin->blocks->rebuildConfig($e);
            Themes::$plugin->displays->rebuildConfig($e);
            Themes::$plugin->groups->rebuildConfig($e);
            Themes::$plugin->fields->rebuildConfig($e);
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
     * @inheritdoc
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    /**
     * @inheritDoc
     */
    protected function beforeUninstall(): bool
    {
        foreach ($this->registry->all() as $plugin) {
            \Craft::$app->plugins->uninstallPlugin($plugin->handle);
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function afterUninstall()
    {
        \Craft::$app->getProjectConfig()->remove('themes');
    }
}
