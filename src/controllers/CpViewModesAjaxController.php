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
    public function beforeAction($action): bool
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

        $viewModes = array_map(function ($viewMode) {
            $viewMode->validate();
            return $viewMode;
        }, $this->layouts->getById($layoutId)->viewModes);
        return [
            'viewModes' => $viewModes
        ];
    }

    /**
     * Get displays for a view mode
     * 
     * @return array
    */
    public function actionGetDisplays(string $uid)
    {
        return [
            'displays' => Themes::$plugin->viewModes->getByUid($uid)->displays
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

        $hasErrors = false;
        $viewModes = [];
        foreach ($viewModesData as $data) {
            if ($data['id'] ?? null) {
                $viewMode = $this->viewModes->populateFromData($data);
            } else {
                $viewMode = $this->viewModes->create($data);
            }
            if (!$viewMode->validate()) {
                $hasErrors = true;
            }
            $viewModes[] = $viewMode;
        }

        if (!$hasErrors) {
            foreach ($viewModes as $viewMode) {
                $this->viewModes->save($viewMode, false);
            }
            $this->viewModes->cleanUp($viewModes, $layout);
            $message = \Craft::t('themes', 'Displays have been saved successfully');
        } else {
            $this->response->setStatusCode(400);
            $message = \Craft::t('themes', 'Error while saving view modes');
        }

        return [
            'viewModes' => $viewModes,
            'message' => $message
        ];
    }
}