<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\DisplayException;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\Entry;

class CpViewModesAjaxController extends Controller
{
    /**
     * @inheritDoc
     */
    public function beforeAction($action) 
    {
        $this->requirePermission('accessPlugin-themes');
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
     * Get view modes for a theme and a layout
     * 
     * @param  string $theme
     * @param  string $type
     * @param  string $uid
     * @return array
     */
    public function actionViewModes(string $theme, string $type, string $uid = ''): array
    {
        $layout = Themes::$plugin->layouts->get($theme, $type, $uid);
        return [
            'viewModes' => $layout->viewModes
        ];
    }
}