<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\DisplayException;

class CpInstallAjaxController extends Controller
{
    /**
     * @inheritDoc
     */
    public function beforeAction($action) 
    {
        $this->requireAdmin();
        $this->requireAcceptsJson();
        $this->requirePostRequest();
        return true;
    }

    /**
     * @inheritDoc
     */
    public function afterAction($action, $result)
    {
        return $this->asJson($result);
    }

    /**
     * (Re)install all layouts
     * 
     * @return array
     */
    public function actionInstall(): array
    {
        $this->layouts->install();
        \Craft::$app->plugins->savePluginSettings(Themes::$plugin, ['installed' => true]);
        return [
            'message' => \Craft::t('themes', 'Themes data has been installed')
        ];
    }
}