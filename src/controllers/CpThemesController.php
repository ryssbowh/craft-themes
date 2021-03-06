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
        // $this->layouts->createAll();
        return $this->renderTemplate('themes/cp/themes', [
            'title' => \Craft::t('themes', 'Themes'),
        ]);
    }
}