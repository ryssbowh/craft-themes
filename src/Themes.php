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
     * @inheritdoc
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    /**
     * Initializes the plugin.
     */
    public function init()
    {
        parent::init();

        self::$plugin = $this;

        $this->setComponents([
            'registry' => ThemesRegistry::class,
            'rules' => [
                'class' => ThemesRules::class,
                'rules' => $this->getSettings()->rules,
                'default' => $this->getSettings()->default
            ]
        ]);

        \Craft::info('Loading themes plugin', __METHOD__);

        \Yii::setAlias('@themesPath', '@root/themes');
        \Yii::setAlias('@themesWebPath', '@webroot/themes');

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

        if (!Craft::$app->request->getIsSiteRequest()) {
            return ;
        }

        $theme = $this->rules->resolveCurrentTheme();

        if (!$theme) {
            \Craft::info("No theme found for request ".\Craft::$app->request->getFullUri(), __METHOD__);
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
    protected function settingsHtml(): string
    {
        \Craft::$app->view->registerAssetBundle(SettingsAssets::class);
        $themes = $this->themes->getAsNames();
        list($sites, $languages) = $this->parseSites();
        $cols = [
            'enabled' => [
                'heading' => 'Enabled',
                'type' => 'lightswitch',
                'class' => 'thin enabled'
            ],
            'type' => [
                'heading' => 'Type',
                'type' => 'select',
                'options' => [
                    'site' => 'Site',
                    'language' => 'Language',
                    'url' => 'Url'
                ],
                'class' => 'type cell'
            ],
            'url' => [
                'type' => 'type',
                'heading' => 'Url',
                'class' => 'url cell',
                'placeholder' => 'Enter url here'
            ],
            'site' => [
                'heading' => 'Site',
                'type' => 'select',
                'options' => $sites,
                'class' => 'site cell'
            ],
            'language' => [
                'heading' => 'Language',
                'type' => 'select',
                'options' => $languages,
                'class' => 'language cell'
            ],
            'theme' => [
                'heading' => 'Theme',
                'type' => 'select',
                'options' => $themes,
                'class' => 'theme cell'
            ]
        ];
        return Craft::$app->view->renderTemplate('themes/_settings', [
            'settings' => $this->getSettings(),
            'cols' => $cols,
            'themes' => ['' => 'No theme'] + $themes,
            'settings' => $this->getSettings()
        ]);
    }
}
