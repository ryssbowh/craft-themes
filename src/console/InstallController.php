<?php
namespace Ryssbowh\CraftThemes\console;

use Ryssbowh\CraftThemes\Themes;
use craft\console\Controller;

class InstallController extends Controller
{   
    /**
     * Reinstall all layouts, will delete orphans
     */
    public function actionIndex()
    {
        Themes::$plugin->layouts->install(true);
        return ExitCode::OK;
    }
}