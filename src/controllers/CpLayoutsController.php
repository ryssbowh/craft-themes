<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\assets\LayoutsAssets;
use Ryssbowh\CraftThemes\models\layouts\Layout;

class CpLayoutsController extends Controller
{
    /**
     * Layouts index
     * 
     * @param  string|null $themeName
     * @param  int|null    $layout
     * @return Response
     */
    public function actionIndex(?string $themeName = null, ?int $layout = null)
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

        if ($layout == null) {
            if ($theme) {
                $layout = $this->layouts->getDefault($themeName);
            }
        } else {
            $layout = $this->layouts->getById($layout);
        }

        $this->view->registerAssetBundle(LayoutsAssets::class);

        // dd($this->layouts->getLayoutsByTheme(true));
        return $this->renderTemplate('themes/cp/layouts', [
            'title' => \Craft::t('themes', 'Layouts'),
            'themes' => $themes,
            'theme' => $themeName,
            'layout' => $layout ? $layout->id : null,
            'allLayouts' => $this->layouts->getLayoutsByTheme(true)
        ]);
    }
}