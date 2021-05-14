<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\DisplayException;

class CpInstallAjaxController extends Controller
{
    public function beforeAction($action) 
    {
        $this->requireAdmin();
        $this->requireAcceptsJson();
        $this->requirePostRequest();
        return true;
    }

    public function afterAction($action, $result)
    {
        return $this->asJson($result);
    }

    /**
     * Repairs all layouts
     * 
     * @return Response
     */
    public function actionInstall()
    {
        $this->layouts->install();
        \Craft::$app->plugins->savePluginSettings(Themes::$plugin, ['installed' => true]);
        return [
            'message' => \Craft::t('themes', 'Themes data has been installed')
        ];
    }
}