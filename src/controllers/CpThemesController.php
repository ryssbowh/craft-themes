<?php
namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\assets\ListAssets;
use Ryssbowh\CraftThemes\scss\Compiler;
use craft\web\Response;

/**
 * Controller for actions related to theme list
 */
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
        if ((!Themes::$plugin->is(Themes::EDITION_PRO) or !\Craft::$app->config->getGeneral()->allowAdminChanges) and in_array($redirectTo, ['display', 'blocks'])) {
            $redirectTo = 'list';
        }
        return $this->redirect('themes/' . $redirectTo);
    }

    /**
     * Themes list
     * 
     * @return Response
     */
    public function actionList()
    {
        $this->requirePermission('accessPlugin-themes');

        \Craft::$app->view->registerAssetBundle(ListAssets::class);

        return $this->renderTemplate('themes/cp/themes', [
            'title' => \Craft::t('themes', 'Themes'),
            'themes' => Themes::$plugin->registry->getAll(),
            'isPro' => Themes::$plugin->is(Themes::EDITION_PRO)
        ]);
    }
}