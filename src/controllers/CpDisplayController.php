<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\assets\DisplayAssets;

class CpDisplayController extends Controller
{
    /**
     * Display index
     * 
     * @param  string|null $themeName
     * @param  int|null    $layout
     * @return Response
     */
    public function actionIndex(?string $themeName = null, int $layout = null)
    {
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

        $this->view->registerAssetBundle(DisplayAssets::class);

        return $this->renderTemplate('themes/cp/display', [
            'title' => \Craft::t('themes', 'Display'),
            'themes' => $themes,
            'theme' => $themeName,
            'allLayouts' => $this->layouts->getLayoutsByTheme(true),
            'layout' => $layout ? $layout : 0
        ]);
    }

    /**
     * Get view modes for a theme and a layout as json
     * 
     * @param  int $layout
     * @return Response
     */
    public function actionViewModes(int $layout)
    {
        $this->requireAcceptsJson();
        $layout = $this->layouts->getById($layout);
        return $this->asJson([
            'viewModes' => $this->viewModes->forLayout($layout)
        ]);
    }
}