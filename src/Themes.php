<?php
namespace Ryssbowh\CraftThemes;

use Craft;
use Ryssbowh\CraftThemes\models\Settings;
use Ryssbowh\CraftThemes\services\ThemesService;
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

        \Yii::setAlias('@themesPath', '@root/themes');
        \Yii::setAlias('@themesWebPath', '@webroot/themes');
        \Yii::setAlias('@themesWeb', '@web/themes');

        Event::on(ClearCaches::class, ClearCaches::EVENT_REGISTER_CACHE_OPTIONS,
            function (RegisterCacheOptionsEvent $event) {
                $event->options[] = [
                    'key' => 'themes-cache',
                    'label' => Craft::t('themes', 'Themes cache'),
                    'action' => function() {
                        ThemesService::clearCaches();
                    }
                ];
            }
        );

        if (!Craft::$app->request->getIsSiteRequest()) {
            return ;
        }

        $site = \Craft::$app->sites->getCurrentSite();
        $theme = $this->themes->setCurrentFromSite($site);

        if (!$theme) {
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

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate('themes/_settings', [
            'settings' => $this->getSettings(),
            'themes' => ['' => 'Default (No theme)'] + $this->themes->getAsNames(),
            'sites' => \Craft::$app->sites->getAllSites()
        ]);
    }
}
