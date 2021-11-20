<?php
namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\DisplayException;
use Ryssbowh\CraftThemes\services\LayoutService;
/**
 * Controller for ajax actions related to view modes
 */
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
     * Get view modes for a layout
     * 
     * @return array
     */
    public function actionGet()
    {
        $layoutId = $this->request->getRequiredParam('layoutId');

        return [
            'viewModes' => $this->layouts->getById($layoutId)->viewModes
        ];
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
        $viewModes = array_map(function ($viewMode) {
            $array = $viewMode->toArray();
            unset($array['displays']);
            return $array;
        }, $layout ? $layout->viewModes : []);
        return [
            'viewModes' => $viewModes
        ];
    }

    /**
     * Saves view modes
     * 
     * @return array
     */
    public function actionSave()
    {
        $layoutId = $this->request->getRequiredParam('layoutId');
        $viewModesData = $this->request->getRequiredParam('viewModes');

        $layout = $this->layouts->getById($layoutId);

        $viewModes = [];
        foreach ($viewModesData as $data) {
            if ($data['id'] ?? null) {
                $viewMode = $this->viewModes->populateFromPost($data);
            } else {
                $viewMode = $this->viewModes->create($data);
            }
            $this->viewModes->save($viewMode);
            $viewModes[] = $viewMode;
        }

        $this->viewModes->cleanUp($viewModes, $layout);

        return [
            'viewModes' => $layout->viewModes,
            'message' => \Craft::t('themes', 'Displays have been saved successfully')
        ];
    }
}