<?php
namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\assets\ListAssets;

class CpThemesController extends Controller
{
    /**
     * Themes index
     * 
     * @return Response
     */
    public function actionIndex()
    {
        $this->requirePermission('accessPlugin-themes');

        $redirectTo = Themes::$plugin->settings->redirectTo;
        if (!\Craft::$app->config->getGeneral()->allowAdminChanges and $redirectTo != 'list') {
            $redirectTo = 'list';
        }
        return $this->redirect('themes/' . $redirectTo);
    }

    public function actionList()
    {
        $this->requirePermission('accessPlugin-themes');

        \Craft::$app->view->registerAssetBundle(ListAssets::class);

        return $this->renderTemplate('themes/cp/themes', [
            'title' => \Craft::t('themes', 'Themes'),
            'themes' => Themes::$plugin->registry->all()
        ]);
    }
}