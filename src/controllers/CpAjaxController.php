<?php 

namespace Ryssbowh\CraftThemes\controllers;

class CpAjaxController extends Controller
{
    public function beforeAction($action) 
    {
        $this->requireAcceptsJson();
        $this->requirePostRequest();
        return true;
    }

    public function afterAction($action, $result)
    {
        return $this->asJson($result);
    }

    /**
     * Get view modes for a theme and a layout as json
     * 
     * @param  int $layout
     * @return Response
     */
    public function actionViewModes(int $layout)
    {
        $layout = $this->layouts->getById($layout);

        return [
            'viewModes' => $this->viewModes->forLayout($layout)
        ];
    }

    /**
     * Get all block providers as json
     * 
     * @return Response
     */
    public function actionProviders()
    {
        return [
            'providers' => $this->blockProviders->getAll(true)
        ];
    }

    /**
     * Get all blocks for a layout as json
     * 
     * @param  int    $layout
     * @return Response
     */
    public function actionBlocks(int $layout)
    {
        $layout = $this->layouts->getById($layout);
        return [
            'blocks' => $this->blocks->forLayout($layout)
        ];
    }

    /**
     * Get all displays for a layout
     * 
     * @param  int $layout
     * @return Response
     */
    public function actionDisplays(int $layout)
    {
        $layout = $this->layouts->getById($layout);
        return [
            'displays' => $this->display->getForLayout($layout),
        ];
    }

    public function actionDisplayOptions()
    {
        $displayId = $this->request->getBodyParam('id');
        $displayerHandle = $this->request->getRequiredParam('displayer');

        if ($displayId) {
            $display = $this->display->getById($displayId);
            $display->item->displayerHandle = $displayerHandle;
            $displayer = $display->item->displayer;
        } else {
            $displayer = $this->fieldDisplayers->getByHandle($displayerHandle);
        }
        return [
            'html' => $displayer->getOptionsHtml()
        ];
    }

    public function actionValidateDisplayOptions()
    {
        $displayId = $this->request->getBodyParam('id');
        $displayerHandle = $this->request->getRequiredParam('displayer');
        $optionsData = $this->request->getRequiredParam('options');

        if ($displayId) {
            $field = $this->display->getById($displayId)->item;
            $field->displayerHandle = $displayerHandle;
            $displayer = $field->displayer;
        } else {
            $displayer = $this->fieldDisplayers->getByHandle($displayerHandle);
        }
        $options = $displayer->getOptions();
        $options->setAttributes($optionsData);
        $options->validate();
        return [
            'errors' => $options->getErrors()
        ];
    }

    public function actionSaveDisplays()
    {
        $displaysData = $this->request->getRequiredParam('displays');
        $layoutId = $this->request->getRequiredParam('layout');
        $viewModeData = $this->request->getRequiredParam('viewModes');
        $layout = $this->layouts->getById($layoutId);

        $viewModeMapping = [];
        $viewModes = [];
        foreach ($viewModeData as $data) {
            $data['layout'] = $layoutId;
            $viewMode = $this->viewModes->fromData($data);
            $this->viewModes->save($viewMode);
            $viewModeMapping[$viewMode->handle] = $viewMode->id;
            $viewModes[] = $viewMode;
        }

        $displays = $displayIds = [];
        foreach ($displaysData as $data) {
            if (is_string($data['viewMode_id'])) {
                $data['viewMode_id'] = $viewModeMapping[$data['viewMode_id']];
            }
            $display = $this->display->fromData($data);
            $this->display->save($display);
            $displays[] = $display;
            $displayIds[] = $display->id;
        }

        $this->viewModes->deleteForLayout($layout, array_values($viewModeMapping));
        $this->display->deleteForLayout($layout, $displayIds);

        return [
            'displays' => $displays,
            'viewModes' => $viewModes,
            'message' => \Craft::t('themes', 'Displays have been saved successfully')
        ];
    }

    /**
     * Delete a layout by id
     * 
     * @param  int    $id
     * @return Response
     */
    public function actionDeleteLayout(int $id)
    {
        $layout = $this->layouts->getById($id);
        $layout->hasBlocks = 0;
        $this->layouts->save($layout);
        $this->blocks->deleteLayoutBlocks($layout);

        return [
            'message' => \Craft::t('themes', 'Layout deleted successfully.'),
            'layout' => $layout
        ];
    }

    /**
     * Save blocks
     * 
     * @return Response
     */
    public function actionSaveLayout()
    {
        $_this = $this;
        $blocksData = $this->request->getRequiredParam('blocks');
        $themeName = $this->request->getRequiredParam('theme');
        $layoutData = $this->request->getRequiredParam('layout');

        $layout = $this->layouts->getById($layoutData['id']);

        if (!$layout->hasBlocks) {
            $layout->hasBlocks = 1;
            $this->layouts->save($layout);
        }

        $blocks = array_map(function ($blockData) use ($_this) {
            return $_this->blocks->fromData($blockData);
        }, $blocksData);

        if (!$this->blocks->saveBlocks($blocks, $layout)) {
            $this->response->setStatusCode(400);
            return $this->asJson([
                'message' => 'Error while saving blocks',
                'errors' => array_map(function ($block) {
                    return $block->getErrors();
                }, $blocks)
            ]);
        }

        return [
            'message' => \Craft::t('themes', 'Blocks saved successfully.'),
            'blocks' => $blocks,
            'layout' => $layout
        ];
    }

    /**
     * Repairs all layouts
     * 
     * @return Response
     */
    public function actionRepair()
    {
        $this->layouts->createAll();
        $this->display->createAll();
        return [
            'message' => \Craft::t('themes', 'Layouts and display have been repaired')
        ];
    }
}