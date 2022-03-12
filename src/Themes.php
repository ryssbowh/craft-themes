<?php
namespace Ryssbowh\CraftThemes;

use Craft;
use Detection\MobileDetect;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\assets\SettingsAssets;
use Ryssbowh\CraftThemes\behaviors\LayoutBehavior;
use Ryssbowh\CraftThemes\behaviors\ProductTypeLayoutBehavior;
use Ryssbowh\CraftThemes\events\RelatedPluginsEvent;
use Ryssbowh\CraftThemes\helpers\PluginsHelper;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\jobs\InstallThemesDataJob;
use Ryssbowh\CraftThemes\models\Settings;
use Ryssbowh\CraftThemes\services\{BlockProvidersService, BlockService, FieldDisplayerService, LayoutService, FieldsService, RulesService, ViewModeService, ViewService, ThemesRegistry, CacheService, DisplayService, GroupService, TablesService, FileDisplayerService, BlockCacheService, GroupsService, ShortcutsService, DisplayerCacheService, EagerLoadingService, CreatorService, ScssService};
use Ryssbowh\CraftThemes\twig\ThemesVariable;
use Ryssbowh\CraftThemes\twig\TwigTheme;
use Twig\Extra\Intl\IntlExtension;
use Twig\TwigTest;
use craft\base\PluginInterface;
use craft\base\Volume;
use craft\commerce\elements\Variant;
use craft\commerce\models\ProductType;
use craft\commerce\services\ProductTypes;
use craft\elements\GlobalSet;
use craft\elements\User;
use craft\events\{ElementEvent, DefineBehaviorsEvent, CategoryGroupEvent, ConfigEvent, EntryTypeEvent, FieldEvent, GlobalSetEvent, RegisterUserPermissionsEvent, TagGroupEvent, VolumeEvent, PluginEvent, RebuildConfigEvent, RegisterCacheOptionsEvent, RegisterCpNavItemsEvent, RegisterTemplateRootsEvent, RegisterUrlRulesEvent, TemplateEvent};
use craft\helpers\Queue;
use craft\models\CategoryGroup;
use craft\models\EntryType;
use craft\models\TagGroup;
use craft\services\{Categories, Plugins, ProjectConfig, Sections, Volumes, UserPermissions, Tags, Globals, Fields, Users, Elements};
use craft\utilities\ClearCaches;
use craft\web\UrlManager;
use craft\web\View;
use craft\web\twig\variables\Cp;
use craft\web\twig\variables\CraftVariable;
use yii\base\Application;
use yii\base\Event;

/**
 * Main plugin class
 */
class Themes extends \craft\base\Plugin
{   
    const EDITION_LITE = 'lite';
    const EDITION_PRO = 'pro';

    const EVENT_RELATED_PLUGINS = 'related_plugins';

    /**
     * @var Themes
     */
    public static $plugin;

    /**
     * @inheritdoc
     */
    public $schemaVersion = '3.1.0';
    
    /**
     * @inheritdoc
     */
    public $hasCpSettings = true;

    /**
     * @inheritdoc
     */
    public $hasCpSection = true;

    protected $_relatedPlugins;

    /**
     * inheritDoc
     */
    public function init()
    {
        parent::init();

        self::$plugin = $this;

        \Craft::setAlias('@themesWebPath', '@webroot/themes');
        \Craft::setAlias('@themesWeb', '@web/themes');

        if (\Craft::$app->request->getIsConsoleRequest()) {
            $this->controllerNamespace = 'Ryssbowh\\CraftThemes\\console';
        }

        $this->registerServices();
        $this->registerPermissions();
        $this->registerClearCacheEvent();
        $this->registerPluginsConfig();
        $this->registerTwig();
        $this->registerBehaviors();
        $this->registerProjectConfig();

        if ($this->is($this::EDITION_PRO)) {
            $this->initPro();
        }

        Event::on(
            Application::class, 
            Application::EVENT_BEFORE_REQUEST, 
            [$this->rules, 'resolveCurrentTheme']
        );
        Event::on(
            View::class, 
            View::EVENT_END_BODY, 
            [$this->registry, 'registerCurrentThemeBundles']
        );
        Event::on(
            View::class, 
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
            [$this->registry, 'registerCurrentThemeTemplates']
        );

        if (Craft::$app->request->getIsCpRequest()) {
            $this->registerCpRoutes();
        }

        \Craft::info('Loaded themes plugin', __METHOD__);
    }

    /**
     * @inheritDoc
     */
    public static function editions(): array
    {
        return [
            self::EDITION_LITE,
            self::EDITION_PRO,
        ];
    }

    /**
     * Get all plugins handles that are related to themes.
     * This list will keep track of plugins for which (un)installation will trigger layouts to be rebuilt
     *
     * @return array
     * @since  3.1.0
     */
    public function getRelatedPlugins(): array
    {
        if ($this->_relatedPlugins === null) {
            $e = new RelatedPluginsEvent;
            $this->trigger(self::EVENT_RELATED_PLUGINS, $e);
            $this->_relatedPlugins = $e->related;
        }
        return $this->_relatedPlugins;
    }

    /**
     * Is a plugin related to themes
     * 
     * @param  string  $handle
     * @return boolean
     * @since  3.1.0
     */
    public function isPluginRelated(string $handle)
    {
        return in_array($handle, $this->relatedPlugins);
    }

    /**
     * @inheritDoc
     */
    public function getCpNavItem()
    {
        $user = \Craft::$app->user;
        if ($user->checkPermission('accessPlugin-themes')) {
            $item = [
                'url' => 'themes',
                'icon' => '@Ryssbowh/CraftThemes/icon-mask.svg',
                'label' => $this->settings->menuItemName ?: \Craft::t('themes', 'Theming'),
                'subnav' => [
                    'themes' => [
                        'url' => 'themes/list',
                        'label' => \Craft::t('themes', 'Themes'),
                    ]
                ]
            ];
            if (\Craft::$app->config->getGeneral()->allowAdminChanges) {
                $isPro = $this->is($this::EDITION_PRO);
                if ($isPro and $user->checkPermission('manageThemesBlocks')) {
                    $item['subnav']['themes-blocks'] = [
                        'url' => 'themes/blocks',
                        'label' => \Craft::t('themes', 'Blocks'),
                    ];
                }
                if ($isPro and $user->checkPermission('manageThemesDisplays')) {
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
        }
        return $item ?? null;
    }

    /**
     * Initialise for pro edition
     */
    protected function initPro()
    {
        $this->registerShortcuts();
        $this->registerCraftEvents();
        $this->registerCpHooks();

        Event::on(
            View::class, 
            View::EVENT_BEFORE_RENDER_PAGE_TEMPLATE,
            [$this->view, 'beforeRenderPage']
        );
    }

    /**
     * Register front end shortcuts
     */
    protected function registerShortcuts()
    {
        if (\Craft::$app->request->isSiteRequest and
            \Craft::$app->user->checkPermission('viewThemesShortcuts')
        ) {
            Event::on(
                ViewService::class,
                ViewService::BEFORE_RENDERING_LAYOUT,
                [$this->shortcuts, 'registerLayout']
            );
        }
    }

    /**
     * Modify twig
     */
    protected function registerTwig()
    {
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                $event->sender->set('themes', ThemesVariable::class);
            }
        );
        $twig = \Craft::$app->view->twig;
        //Add the twig test 'is array'
        $isArray = new TwigTest('array', function ($value) {
            return is_array($value);
        });
        $twig->addTest($isArray);
        //Add the twig test 'is instanceof'
        $isInstance = new TwigTest('instanceof', function ($value, $class) {
            return $value instanceof $class;
        });
        $twig->addTest($isInstance);
        //Add the twig test 'is numeric'
        $isNumeric = new TwigTest('numeric', function ($value) {
            return is_numeric($value);
        });
        $twig->addTest($isNumeric);
        // Registers Twig Intl extension to get the filter `format_datetime`
        // @see https://twig.symfony.com/doc/3.x/filters/format_datetime.html
        $twig->addExtension(new IntlExtension());
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
                'settings' => $this->getSettings(),
                'isPro' => $this->is($this::EDITION_PRO)
            ]
        );
    }

    /**
     * Attach custom behaviors to Entry types, Global sets, Category group, Volumes and Tags
     */
    protected function registerBehaviors()
    {
        $types = [
            EntryType::class => LayoutService::ENTRY_HANDLE,
            CategoryGroup::class => LayoutService::CATEGORY_HANDLE,
            Volume::class => LayoutService::VOLUME_HANDLE,
            TagGroup::class => LayoutService::TAG_HANDLE,
            GlobalSet::class => LayoutService::GLOBAL_HANDLE,
            User::class => LayoutService::USER_HANDLE,
        ];
        foreach ($types as $class => $type) {
            Event::on($class::className(), $class::EVENT_DEFINE_BEHAVIORS, function(DefineBehaviorsEvent $event) use ($type) {
                $event->sender->attachBehaviors([
                    'themeLayout' => [
                        'class' => LayoutBehavior::class,
                        'type' => $type
                    ]
                ]);
            });
        }
        // Add product type behavior, this is not possible as of now
        // @see https://github.com/craftcms/commerce/issues/2715
        // Event::on(ProductType::class, ProductType::EVENT_DEFINE_BEHAVIORS, function(DefineBehaviorsEvent $event) use ($type) {
        //     $event->sender->attachBehaviors([
        //         'themeLayout' => [
        //             'class' => ProductTypeLayoutBehavior::class
        //         ]
        //     ]);
        // });
    }

    /**
     * Register to project config changes related to plugins
     */
    protected function registerPluginsConfig()
    {
        $_this = $this;
        \Craft::$app->projectConfig
            ->onAdd(Plugins::CONFIG_PLUGINS_KEY . '.{uid}', function (ConfigEvent $e) {
                PluginsHelper::onAdd($e->tokenMatches[0], $e->newValue);
            })
            ->onRemove(Plugins::CONFIG_PLUGINS_KEY . '.{uid}', function (ConfigEvent $e) {
                PluginsHelper::onRemove($e->tokenMatches[0], $e->oldValue);
            })
            ->onUpdate(Plugins::CONFIG_PLUGINS_KEY . '.{uid}', function (ConfigEvent $e) {
                // dd($e);
                PluginsHelper::onUpdate($e->tokenMatches[0], $e->oldValue, $e->newValue);
            });

        //Install theme's dependencies before it's installed
        Event::on(Plugins::class, Plugins::EVENT_BEFORE_INSTALL_PLUGIN, function (PluginEvent $event) {
            if ($event->plugin instanceof ThemeInterface) {
                $extends = $event->plugin->extends;
                if ($extends) {
                    \Craft::$app->plugins->installPlugin($extends);
                }
            }
        });

        // Install theme's data after a theme is installed
        Event::on(Plugins::class, Plugins::EVENT_AFTER_INSTALL_PLUGIN, function (PluginEvent $event) {
            if ($event->plugin instanceof ThemeInterface) {
                Themes::$plugin->registry->installTheme($event->plugin);
            }
        });

        // Uninstall theme's data before a theme is uninstalled
        Event::on(Plugins::class, Plugins::EVENT_BEFORE_UNINSTALL_PLUGIN, function (PluginEvent $event) {
            if ($event->plugin instanceof ThemeInterface) {
                Themes::$plugin->registry->uninstallTheme($event->plugin);
            }
        });
    }

    /**
     * Register cp routes
     */
    protected function registerCpRoutes()
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules = array_merge($event->rules, [
                'themes' => 'themes/cp-themes',
                'themes/list' => 'themes/cp-themes/list'
            ]);
            if (\Craft::$app->config->getGeneral()->allowAdminChanges) {
                $event->rules = array_merge($event->rules, [
                    'themes/rules' => 'themes/cp-rules',
                    'themes/save-rules' => 'themes/cp-rules/save',
                ]);
                if ($this->is($this::EDITION_PRO)) {
                    $event->rules = array_merge($event->rules, [
                        'themes/blocks' => 'themes/cp-blocks',
                        'themes/blocks/<themeName:[\w-]+>' => 'themes/cp-blocks',
                        'themes/blocks/<themeName:[\w-]+>/<layout:\d+>' => 'themes/cp-blocks',
                        'themes/display' => 'themes/cp-display',
                        'themes/display/<themeName:[\w-]+>' => 'themes/cp-display',
                        'themes/display/<themeName:[\w-]+>/<layout:\d+>' => 'themes/cp-display',
                        'themes/display/<themeName:[\w-]+>/<layout:\d+>/<viewModeHandle:[\w-]+>' => 'themes/cp-display',

                        'themes/ajax/blocks/save' => 'themes/cp-blocks-ajax/save-blocks',
                        'themes/ajax/layouts/delete/<id:\d+>' => 'themes/cp-blocks-ajax/delete-layout',
                        'themes/ajax/blocks/<layout:\d+>' => 'themes/cp-blocks-ajax/blocks',
                        'themes/ajax/block-providers' => 'themes/cp-blocks-ajax/block-providers',

                        'themes/ajax/validate-field-options' => 'themes/cp-display-ajax/validate-field-options',
                        'themes/ajax/install' => 'themes/cp-ajax/install',

                        'themes/ajax/view-modes/<theme:[\w-]+>/<type:[\w]+>/<uid:[\w-]+>' => 'themes/cp-view-modes-ajax/view-modes',
                        'themes/ajax/view-modes/<theme:[\w-]+>/<type:[\w]+>' => 'themes/cp-view-modes-ajax/view-modes',
                        'themes/ajax/view-modes/save' => 'themes/cp-view-modes-ajax/save',
                        'themes/ajax/view-modes' => 'themes/cp-view-modes-ajax/get',
                        'themes/ajax/view-modes/displays/<uid:[\w-]+>' => 'themes/cp-view-modes-ajax/get-displays'
                    ]);
                }
            }
        });
    }

    /**
     * Register services
     */
    protected function registerServices()
    {
        $user = \Craft::$app->user->getIdentity();
        $this->setComponents([
            'registry' => ThemesRegistry::class,
            'rules' => [
                'class' => RulesService::class,
                'rules' => $this->settings->themesRules,
                'default' => $this->settings->default,
                'cache' => \Craft::$app->cache,
                'cacheEnabled' => $this->settings->rulesCacheEnabled,
                'console' => $this->settings->console,
                'setConsole' => $this->settings->setConsole,
                'cp' => $this->settings->cp,
                'setCp' => $this->settings->setCp,
                'mobileDetect' => new MobileDetect(),
            ],
            'shortcuts' => [
                'class' => ShortcutsService::class,
                'showShortcuts' => $user ? $user->getPreference('themesShowShorcuts', false) : false
            ],
            'layouts' => LayoutService::class,
            'blockProviders' => BlockProvidersService::class,
            'blocks' => BlockService::class,
            'viewModes' => ViewModeService::class,
            'fieldDisplayers' => FieldDisplayerService::class,
            'view' => [
                'class' => ViewService::class,
                'cache' => \Craft::$app->cache,
                'eagerLoad' => $this->settings->eagerLoad,
                'devMode' => $user ? $user->getPreference('themesDevMode', false) : false,
                'templateCacheEnabled' => $this->settings->templateCacheEnabled
            ],
            'blockCache' => [
                'class' => BlockCacheService::class,
                'cache' => \Craft::$app->cache,
                'cacheEnabled' => $this->settings->blockCacheEnabled
            ],
            'displayerCache' => [
                'class' => DisplayerCacheService::class,
                'cache' => \Craft::$app->cache,
                'cacheEnabled' => $this->settings->displayerCacheEnabled
            ],
            'eagerLoading' => [
                'class' => EagerLoadingService::class,
                'cache' => \Craft::$app->cache,
                'cacheEnabled' => $this->settings->eagerLoadingCacheEnabled
            ],
            'displays' => DisplayService::class,
            'fields' => FieldsService::class,
            'tables' => TablesService::class,
            'fileDisplayers' => FileDisplayerService::class,
            'groups' => GroupsService::class,
            'creator' => CreatorService::class,
            'scss' => ScssService::class
        ]);
    }

    /**
     * Registers Clear cache options
     */
    protected function registerClearCacheEvent()
    {
        Event::on(ClearCaches::class, ClearCaches::EVENT_REGISTER_CACHE_OPTIONS, function (RegisterCacheOptionsEvent $event) {
            $event->options[] = [
                'key' => 'themes-scss-cache',
                'label' => \Craft::t('themes', 'Inline Scss cache'),
                'action' => function () {
                    Themes::$plugin->scss->clearCaches();
                }
            ];
        });
        Event::on(ClearCaches::class, ClearCaches::EVENT_REGISTER_TAG_OPTIONS, function (RegisterCacheOptionsEvent $event) {
            $event->options[] = [
                'tag' => RulesService::RULES_CACHE_TAG,
                'label' => Craft::t('themes', 'Themes rules')
            ];
            if ($this->is($this::EDITION_PRO)) {
                $event->options[] = [
                    'tag' => ViewService::TEMPLATE_CACHE_TAG,
                    'label' => Craft::t('themes', 'Themes templates resolution')
                ];
                $event->options[] = [
                    'tag' => BlockCacheService::BLOCK_CACHE_TAG,
                    'label' => Craft::t('themes', 'Themes blocks')
                ];
                $event->options[] = [
                    'tag' => DisplayerCacheService::DISPLAYER_CACHE_TAG,
                    'label' => Craft::t('themes', 'Themes displayers')
                ];
                $event->options[] = [
                    'tag' => EagerLoadingService::EAGERLOAD_CACHE_TAG,
                    'label' => Craft::t('themes', 'Themes view modes eager loading')
                ];
            }
        });
    }

    /**
     * Modify some cp pages through hooks
     */
    protected function registerCpHooks()
    {
        if (\Craft::$app->config->getGeneral()->allowAdminChanges and $this->settings->showCpShortcuts) {
            Craft::$app->view->hook('cp.users.edit.prefs', function (array &$context) {
                return \Craft::$app->view->renderTemplate('themes/cp/userprefs', ['user' => $context['currentUser']]);
            });
            Craft::$app->view->hook('cp.entries.edit.details', function (array &$context) {
                return \Craft::$app->view->renderTemplate('themes/cp/editelement', ['element' => $context['entryType']]);
            });
            Craft::$app->view->hook('cp.globals.edit.content', function (array &$context) {
                return \Craft::$app->view->renderTemplate('themes/cp/editelement', [
                    'element' => $context['globalSet'],
                    'hasTopMargin' => true
                ]);
            });
            Craft::$app->view->hook('cp.categories.edit.details', function (array &$context) {
                return \Craft::$app->view->renderTemplate('themes/cp/editelement', ['element' => $context['group']]);
            });
            Craft::$app->view->hook('cp.assets.edit.details', function (array &$context) {
                return \Craft::$app->view->renderTemplate('themes/cp/editelement', ['element' => $context['volume']]);
            });
            Craft::$app->view->hook('cp.users.edit.details', function (array &$context) {
                return \Craft::$app->view->renderTemplate('themes/cp/editelement', ['element' => $context['user']]);
            });
        }
    }

    /**
     * Registers to Entry types, Category groups, Volumes, Tag sets, Global sets, User layouts and Fields deletions/additions events
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
            ->onUpdate(Users::CONFIG_USERLAYOUT_KEY, function (ConfigEvent $e) use ($layouts) {
                $layouts->onCraftElementSaved(LayoutService::USER_HANDLE);
            });

        if (\Craft::$app->plugins->getPlugin('commerce')) {
            Craft::$app->projectConfig
                ->onAdd(ProductTypes::CONFIG_PRODUCTTYPES_KEY.'.{uid}', function (ConfigEvent $e) use ($layouts) {
                    $layouts->onCraftElementSaved(LayoutService::PRODUCT_HANDLE, $e->tokenMatches[0]);
                })
                ->onUpdate(ProductTypes::CONFIG_PRODUCTTYPES_KEY.'.{uid}', function (ConfigEvent $e) use ($layouts) {
                    $layouts->onCraftElementSaved(LayoutService::PRODUCT_HANDLE, $e->tokenMatches[0]);
                })
                ->onRemove(ProductTypes::CONFIG_PRODUCTTYPES_KEY.'.{uid}', function (ConfigEvent $e) use ($layouts) {
                    $layouts->onCraftElementDeleted($e->tokenMatches[0]);
                });
        }

        Event::on(Fields::class, Fields::EVENT_AFTER_SAVE_FIELD, function (FieldEvent $e) {
            Themes::$plugin->fields->onCraftFieldSaved($e);
        });

        Event::on(Elements::class, Elements::EVENT_AFTER_SAVE_ELEMENT, function(ElementEvent $event) {
            if ($event->element instanceof User) {
                $user = $event->element;
                $preferences = [
                    'themesDevMode' => (bool)$this->request->getBodyParam('themesDevMode', $user->getPreference('themesDevMode', false)),
                    'themesShowShorcuts' => (bool)$this->request->getBodyParam('themesShowShorcuts', $user->getPreference('themesShowShorcuts', false)),
                ];
                \Craft::$app->users->saveUserPreferences($user, $preferences);
            }
        });
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

    /**
     * Registers permissions
     */
    protected function registerPermissions()
    {
        if (\Craft::$app->getEdition() !== \Craft::Solo) {
            Event::on(
                UserPermissions::class,
                UserPermissions::EVENT_REGISTER_PERMISSIONS,
                function (RegisterUserPermissionsEvent $event) {
                    $perms = [
                        'manageThemesRules' => [
                            'label' => \Craft::t('themes', 'Manage rules')
                        ]
                    ];
                    if ($this->is($this::EDITION_PRO)) {
                        $perms = array_merge($perms, [
                            'manageThemesBlocks' => [
                                'label' => \Craft::t('themes', 'Manage blocks')
                            ],
                            'manageThemesDisplays' => [
                                'label' => \Craft::t('themes', 'Manage displays')
                            ],
                            'viewThemesShortcuts' => [
                                'label' => \Craft::t('themes', 'View frontend shortcuts')
                            ]
                        ]);
                    }
                    $event->permissions[\Craft::t('themes', 'Themes')] = $perms;
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
    protected function afterUninstall()
    {
        \Craft::$app->getProjectConfig()->remove('themes');
    }
}
