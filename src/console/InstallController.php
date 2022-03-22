<?php
namespace Ryssbowh\CraftThemes\console;

use Ryssbowh\CraftThemes\Themes;
use craft\console\Controller;
use yii\console\ExitCode;

class InstallController extends Controller
{   
    /**
     * Reinstall all layouts, will delete orphans
     */
    public function actionIndex()
    {
        Themes::$plugin->layouts->installAll();
        $this->stdout(\Craft::t('themes', 'Themes data has been installed') . "\n");
        return ExitCode::OK;
    }
}