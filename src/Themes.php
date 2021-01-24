<?php
namespace Ryssbowh\Themes;

use Craft;
use Inspire\Themes\models\Settings;
use Inspire\Themes\services\ThemeRegistry;
use Inspire\Themes\twig\TwigTheme;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\TemplateEvent;
use craft\helpers\App;
use craft\web\UrlManager;
use craft\web\View;
use yii\base\Event;
use yii\log\Logger;

class Themes extends \craft\base\Plugin
{
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

        \Yii::setAlias('themesPath', '@root/themes');
        \Yii::setAlias('themesPublicPath', '@webroot/themes');

        if (Craft::$app->request->getIsSiteRequest()) {
            $current = $this->themes->getCurrent();
            \Yii::setAlias('themePath', '@root/themes/'.$theme->getHandle());
            \Yii::setAlias('themePublicPath', '@webroot/themes/'.$theme->getHandle());
            Craft::$app->view->registerTwigExtension(new TwigTheme);
        }

        //Register templates event hook
        $_this = $this;
        Event::on(
            View::class,
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
            function(RegisterTemplateRootsEvent $event) use ($_this) {
                $_this->registerSiteTemplates($event);
            }
        );
        //Register assets event hook
        Event::on(
            View::class,
            View::EVENT_BEFORE_RENDER_PAGE_TEMPLATE,
            function(TemplateEvent $event) use ($_this) {
                if ($event->templateMode == View::TEMPLATE_MODE_SITE) {
                    $_this->registerSiteAssets($event);
                }
            }
        );
    }

    /**
     * Add the current theme template paths to template roots
     * @param $event
     */
    public function registerSiteTemplates($event)
    {
        $theme = $this->themes->getCurrent();
        if ($theme) {
            Craft::getLogger()->log('register theme templates', Logger::LEVEL_INFO, 'themes');
            $event->roots[''] = array_merge($theme->getTemplatePaths(), $event->roots[''] ?? []);
        }
    }

    /**
     * Add the current theme assets
     * @param $event
     */
    public function registerSiteAssets($event)
    {
        Craft::getLogger()->log('register theme assets', Logger::LEVEL_INFO, 'themes');
        $theme = $this->themes->getCurrent();
        if ($theme) {
            $theme->registerAssets();
        }
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        $settings = $this->getSettings();
        $settings->validate();

        return Craft::$app->view->renderTemplate('themes/_settings', [
            'settings' => $settings,
            'themes' => ['' => 'Default (No theme)'] + $this->themes->getAsNames()
        ]);
    }
}
