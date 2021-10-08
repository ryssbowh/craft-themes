<?php
namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\assets\DisplayAssets;
use Ryssbowh\CraftThemes\events\RegisterBundles;

class CpDisplayController extends Controller
{
    const REGISTER_ASSET_BUNDLES = 'register_asset_bundles';
    
    /**
     * Display index
     * 
     * @param  string|null $themeName
     * @param  int|null    $layout
     * @return Response
     */
    public function actionIndex(?string $themeName = null, int $layout = null, string $viewModeHandle = '')
    {
        $this->requirePermission('manageThemesDisplay');
        $themes = $this->registry->getNonPartials(false, true);
        $theme = null;

        if ($themeName == null) {
            if (sizeof($themes)) {
                $keys = array_keys($themes);
                $themeName = $keys[0];
                $theme = $themes[$keys[0]];
            }
        } else {
            $theme = $this->registry->getTheme($themeName);
        }

        if ($this->hasEventHandlers(self::REGISTER_ASSET_BUNDLES)) {
            $event = new RegisterBundles;
            $this->trigger(self::REGISTER_ASSET_BUNDLES, $event);
            foreach ($event->bundles as $class) {
                $this->view->registerAssetBundle($class);
            }
        }
        $this->view->registerAssetBundle(DisplayAssets::class);

        return $this->renderTemplate('themes/cp/display', [
            'title' => \Craft::t('themes', 'Display'),
            'themes' => $themes,
            'theme' => $themeName,
            'allLayouts' => $this->layouts->getWithDisplays(),
            'layout' => $layout ? $layout : 0,
            'viewModeHandle' => $viewModeHandle,
        ]);
    }
}