<?php
namespace Ryssbowh\CraftThemes;

use Craft;
use Ryssbowh\CraftThemes\assets\SettingsAssets;
use Ryssbowh\CraftThemes\blockProviders\SystemBlockProvider;
use Ryssbowh\CraftThemes\events\RegisterBlockProvidersEvent;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\Layout;
use Ryssbowh\CraftThemes\models\Settings;
use Ryssbowh\CraftThemes\services\BlockProvidersService;
use Ryssbowh\CraftThemes\services\BlockService;
use Ryssbowh\CraftThemes\services\LayoutService;
use Ryssbowh\CraftThemes\services\ThemesRegistry;
use Ryssbowh\CraftThemes\services\ThemesRules;
use Ryssbowh\CraftThemes\twig\TwigTheme;
use craft\events\RebuildConfigEvent;
use craft\events\RegisterCacheOptionsEvent;
use craft\events\RegisterCpNavItemsEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\TemplateEvent;
use craft\helpers\App;
use craft\services\ProjectConfig;
use craft\utilities\ClearCaches;
use craft\web\UrlManager;
use craft\web\View;
use craft\web\twig\variables\Cp;
use yii\base\Event;
use yii\log\Logger;

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

        \Craft::info('Loaded themes plugin, handling current request...', __METHOD__);

        if (Craft::$app->request->getIsSiteRequest()) {
            $theme = $this->handleCurrentRequest();
            if ($theme) {
                $this->registerPageTemplate($theme);
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

    protected function registerPageTemplate(ThemeInterface $theme)
    {
        Event::on(View::class, View::EVENT_BEFORE_RENDER_PAGE_TEMPLATE, function(TemplateEvent $event) use ($theme) {
            $event->variables['layout'] = $theme->getPageLayout();
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
                    'themes/blocks' => 'themes/cp-blocks',
                    'themes/blocks/<themeName:\w+>' => 'themes/cp-blocks',
                    'themes/blocks/<themeName:\w+>/save' => 'themes/cp-blocks/save-layout',
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
            'registry' => [
                'class' => ThemesRegistry::class,
                'folder' => \Yii::getAlias('@themesPath'),
            ],
            'rules' => [
                'class' => ThemesRules::class,
                'rules' => $this->getSettings()->rules,
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
            ->onAdd(LayoutService::LAYOUTS_CONFIG_KEY.'.{uid}',      [$this->layouts,   'handleChanged'])
            ->onUpdate(LayoutService::LAYOUTS_CONFIG_KEY.'.{uid}',   [$this->layouts,   'handleChanged'])
            ->onRemove(LayoutService::LAYOUTS_CONFIG_KEY.'.{uid}',   [$this->layouts,   'handleDeleted']);

        Event::on(ProjectConfig::class, ProjectConfig::EVENT_REBUILD, function(RebuildConfigEvent $e) {
            Themes::$plugin->layouts->rebuildLayoutConfig($e);
        });
    }

    /**
     * Resolves current theme and registers aliases, templates & hooks
     */
    protected function handleCurrentRequest(): ?ThemeInterface
    {
        $theme = $this->rules->resolveCurrentTheme();
        $this->registry->setCurrent($theme);

        if (!$theme) {
            \Craft::info("No theme found for request ".\Craft::$app->request->getUrl(), __METHOD__);
            return null;
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

        return $theme;
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
