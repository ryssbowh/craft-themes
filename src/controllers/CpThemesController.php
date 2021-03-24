<?php 

namespace Ryssbowh\CraftThemes\controllers;

class CpThemesController extends Controller
{
    /**
     * Themes index
     * 
     * @return Response
     */
    public function actionIndex()
    {
        return $this->renderTemplate('themes/cp/themes', [
            'title' => \Craft::t('themes', 'Themes'),
        ]);
    }
}