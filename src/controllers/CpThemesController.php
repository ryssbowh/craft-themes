<?php
namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\assets\ListAssets;
use Ryssbowh\CraftThemes\helpers\Templates;
use Ryssbowh\CraftThemes\scss\Compiler;
use craft\web\Response;
use craft\web\View;

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

    /**
     * Returns which templates are overriden for a theme
     *
     * @since 4.2.0
     */
    public function actionOverriddenTemplates()
    {
        $theme = $this->request->getRequiredParam('theme');
        $theme = Themes::$plugin->registry->getTheme($theme);
        return $this->renderTemplate('themes/cp/theme-templates', [
            'theme' => $theme,
            'templates' => Templates::getOverriddenForTheme($theme)
        ], View::TEMPLATE_MODE_CP);
    }
}