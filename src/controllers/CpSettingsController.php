<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\assets\SettingsAssets;

class CpSettingsController extends Controller
{
    /**
     * Settings index
     * 
     * @return Response
     */
    public function actionIndex()
    {
        $currentUser = \Craft::$app->user;
        if (!$currentUser->getIsAdmin()) {
            throw new HttpException(403);
        }

        $settings = Themes::$plugin->getSettings();
        $settings->validate();

        $namespace = 'settings';
        \Craft::$app->view->registerAssetBundle(SettingsAssets::class);
        $themes = $this->registry->getNonPartials(true);
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
        return $this->renderTemplate('themes/cp/settings', [
            'title' => \Craft::t('themes', 'Settings'),
            'settings' => $settings,
            'cols' => $cols,
            'themes' => ['' => \Craft::t('themes', 'No theme')] + $themes,
            'namespace' => 'settings'
        ]);
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