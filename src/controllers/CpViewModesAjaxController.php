<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\DisplayException;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\Entry;

class CpViewModesAjaxController extends Controller
{
    public function beforeAction($action) 
    {
        $this->requirePermission('accessPlugin-themes');
        $this->requireAcceptsJson();
        $this->requirePostRequest();
        return true;
    }

    public function afterAction($action, $result)
    {
        return $this->asJson($result);
    }

    public function actionViewModes(string $theme, string $type, string $uid = '')
    {
        $layout = Themes::$plugin->layouts->get($theme, $type, $uid);
        return [
            'viewModes' => $layout->viewModes
        ];
    }
}