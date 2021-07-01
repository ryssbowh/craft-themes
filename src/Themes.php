<?php
namespace Ryssbowh\CraftThemes;

use Craft;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\assets\SettingsAssets;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\Settings;
use Ryssbowh\CraftThemes\services\{BlockProvidersService, BlockService, FieldDisplayerService, LayoutService, FieldsService, RulesService, ViewModeService, ViewService, ThemesRegistry, CacheService, DisplayService, GroupService, MatrixService, TablesService, FileDisplayerService, BlockCacheService};
use Ryssbowh\CraftThemes\twig\ThemesVariable;
use Ryssbowh\CraftThemes\twig\TwigTheme;
use craft\base\PluginInterface;
use craft\events\SectionEvent;
use craft\events\{CategoryGroupEvent, ConfigEvent, EntryTypeEvent, FieldEvent, GlobalSetEvent, RegisterUserPermissionsEvent, TagGroupEvent, VolumeEvent, PluginEvent, RebuildConfigEvent, RegisterCacheOptionsEvent, RegisterCpNavItemsEvent, RegisterTemplateRootsEvent, RegisterUrlRulesEvent, TemplateEvent};
use craft\helpers\UrlHelper;
use craft\services\{Categories, Plugins, ProjectConfig, Sections, Volumes, UserPermissions, Tags, Globals, Fields};
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
                    'url' => 'themes',
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
        Event::on(Plugins::class, Plugins::EVENT_AFTER_DISABLE_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin instanceof ThemeInterface) {
                    Themes::$plugin->rules->flushCache();
                }
            }
        );

        Event::on(Plugins::class, Plugins::EVENT_BEFORE_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin instanceof ThemeInterface) {
                    Themes::$plugin->installDependency($event->plugin);
                }
            }
        );

        Event::on(Plugins::class, Plugins::EVENT_AFTER_UNINSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin instanceof ThemeInterface) {
                    Themes::$plugin->layouts->uninstallThemeData($event->plugin->handle);
                    Themes::$plugin->rules->flushCache();
                }
            }
        );

        Event::on(Plugins::class, Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin instanceof ThemeInterface) {
                    Themes::$plugin->layouts->installThemeData($event->plugin->handle);
                    Themes::$plugin->blocks->installContentBlock($event->plugin);
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
                'themes' => 'themes/cp-themes'
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

                    'themes/ajax/displays/<layout:\d+>' => 'themes/cp-display-ajax/displays',
                    'themes/ajax/displays/save' => 'themes/cp-display-ajax/save-layout',
                    'themes/ajax/view-modes/<layout:\d+>' => 'themes/cp-display-ajax/view-modes',
                    'themes/ajax/blocks/save' => 'themes/cp-blocks-ajax/save-blocks',
                    'themes/ajax/layouts/delete/<id:\d+>' => 'themes/cp-blocks-ajax/delete-layout',
                    'themes/ajax/blocks/<layout:\d+>' => 'themes/cp-blocks-ajax/blocks',
                    'themes/ajax/block-providers' => 'themes/cp-blocks-ajax/block-providers',
                    'themes/ajax/field-options/validate' => 'themes/cp-display-ajax/validate-field-options',
                    'themes/ajax/install' => 'themes/cp-install-ajax/install',

                    'themes/ajax/entries/<uid:[\w-]+>' => 'themes/cp-ajax/entries',
                    'themes/ajax/categories/<uid:[\w-]+>' => 'themes/cp-ajax/categories',
                    'themes/ajax/users' => 'themes/cp-ajax/users',

                    'themes/ajax/viewModes/<theme:[\w-]+>/<type:[\w]+>/<uid:[\w-]+>' => 'themes/cp-view-modes-ajax/view-modes',
                    'themes/ajax/viewModes/<theme:[\w-]+>/<type:[\w]+>' => 'themes/cp-view-modes-ajax/view-modes'
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
                'cacheEnabled' => $this->getSettings()->rulesCacheEnabled
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
            'display' => DisplayService::class,
            'fields' => FieldsService::class,
            'matrix' => MatrixService::class,
            'tables' => TablesService::class,
            'fileDisplayers' => FileDisplayerService::class,
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
     * Listens to Entries/Categories/Fields deletions and additions
     */
    protected function registerCraftEvents()
    {
        Event::on(Sections::class, Sections::EVENT_AFTER_SAVE_SECTION, function (SectionEvent $e) {
            $type = LayoutService::ENTRY_HANDLE;
            foreach ($e->section->entryTypes as $entryType) {
                $uid = $entryType->uid;
                Themes::$plugin->layouts->onCraftElementSaved($type, $uid);
            }
        });
        Event::on(Sections::class, Sections::EVENT_AFTER_SAVE_ENTRY_TYPE, function (EntryTypeEvent $e) {
            $type = LayoutService::ENTRY_HANDLE;
            $uid = $e->entryType->uid;
            Themes::$plugin->layouts->onCraftElementSaved($type, $uid);
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
        });
        Event::on(Tags::class, Tags::EVENT_AFTER_DELETE_GROUP, function (TagGroupEvent $e) {
            $type = LayoutService::TAG_HANDLE;
            $uid = $e->tagGroup->uid;
            Themes::$plugin->layouts->onCraftElementDeleted($type, $uid);
        });
        Event::on(Fields::class, Fields::EVENT_AFTER_SAVE_FIELD, function (FieldEvent $e) {
            Themes::$plugin->layouts->onCraftFieldSaved($e);
        });
        Event::on(Fields::class, Fields::EVENT_AFTER_DELETE_FIELD, function (FieldEvent $e) {
            Themes::$plugin->layouts->onCraftFieldDeleted($e);
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
            ->onRemove(LayoutService::CONFIG_KEY.'.{uid}',   [$this->layouts, 'handleDeleted']);

        Event::on(ProjectConfig::class, ProjectConfig::EVENT_REBUILD, function(RebuildConfigEvent $e) {
            Themes::$plugin->layouts->rebuildLayoutConfig($e);
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
