<?php
namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\assets\RulesAssets;
use Ryssbowh\CraftThemes\services\RulesService;
use craft\web\Response;

/**
 * Controller for actions related to rules
 */
class CpRulesController extends Controller
{
    /**
     * @inheritDoc
     */
    public function beforeAction($action) 
    {
        $this->requirePermission('manageThemesRules');
        return true;
    }

    /**
     * Rules index
     * 
     * @return Response
     */
    public function actionIndex()
    {
        $settings = Themes::$plugin->getSettings();
        $settings->validate();
        $namespace = 'settings';
        \Craft::$app->view->registerAssetBundle(RulesAssets::class);
        $themes = $this->registry->getNonPartials(true);
        list($sites, $languages) = $this->parseSites();
        $cols = [
            'enabled' => [
                'heading' => \Craft::t('app', 'Enabled'),
                'type' => 'lightswitch',
                'class' => 'thin enabled'
            ],
            'url' => [
                'type' => 'type',
                'heading' => \Craft::t('themes', 'Path or regex (example /^blog*/)'),
                'class' => 'url cell',
                'placeholder' => \Craft::t('themes', 'Enter path here')
            ],
            'site' => [
                'heading' => \Craft::t('app', 'Site'),
                'type' => 'select',
                'options' => ['' => \Craft::t('themes', 'Any')] + $sites,
                'class' => 'site cell'
            ],
            'viewPort' => [
                'heading' => \Craft::t('themes', 'View port'),
                'type' => 'select',
                'options' => [
                    '' => \Craft::t('themes', 'Any'),
                    'phone' => \Craft::t('themes', 'Phone'),
                    'tablet' => \Craft::t('themes', 'Tablet'),
                    'desktop' => \Craft::t('themes', 'Desktop'),
                ],
                'class' => 'view-port cell'
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
        return $this->renderTemplate('themes/cp/rules', [
            'title' => \Craft::t('themes', 'Rules'),
            'settings' => $settings,
            'cols' => $cols,
            'themes' => $themes,
            'namespace' => 'settings'
        ]);
    }

    /**
     * Save rules
     * 
     * @return Response
     */
    public function actionSave()
    {
        $this->requirePostRequest();

        $settings = $this->request->getRequiredParam('settings');
        $plugin = \Craft::$app->getPlugins()->getPlugin('themes');
        if (!$settings['themesRules']) {
            $settings['themesRules'] = [];
        }

        if (!\Craft::$app->getPlugins()->savePluginSettings($plugin, $settings)) {
            $this->setFailFlash(\Craft::t('themes', 'Couldn’t save theme rules'));
        } else {
            Themes::$plugin->rules->flushCache();
            $this->setSuccessFlash(\Craft::t('themes', 'Rules have been saved'));
        }
        $this->redirect('themes/rules');
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
     * @return array
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
}