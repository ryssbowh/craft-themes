<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\DisplayException;

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
    public function actionBlockProviders()
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
            'blocks' => $this->blocks->getForLayout($layout)
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

    public function actionFieldOptions()
    {
        $fieldId = $this->request->getBodyParam('id');
        $displayerHandle = $this->request->getRequiredParam('displayer');

        $field = $this->fields->getById($fieldId);
        $displayer = $this->fieldDisplayers->getByHandle($displayerHandle);
        $displayer->field = $field;
        return [
            'html' => $displayer->getOptionsHtml()
        ];
    }

    public function actionValidateFieldOptions()
    {
        $fieldId = $this->request->getBodyParam('id');
        $displayerHandle = $this->request->getRequiredParam('displayer');
        $optionsData = $this->request->getRequiredParam('options');

        if ($fieldId) {
            $field = $this->fields->getById($fieldId);
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
            $data['layout_id'] = $layoutId;
            $viewMode = $this->viewModes->create($data);
            $viewModeMapping[$viewMode->handle] = $viewMode->id;
            $viewModes[] = $viewMode;
        }
        $layout->viewModes = $viewModes;
        $this->layouts->save($layout);

        $displays = [];
        foreach ($displaysData as $data) {
            if (is_string($data['viewMode_id'])) {
                $data['viewMode_id'] = $viewModeMapping[$data['viewMode_id']];
            }
            $display = $this->display->create($data);
            $this->display->save($display);
            $displays[] = $display;
        }

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
        $layout->blocks = [];
        $this->layouts->save($layout);

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
    public function actionSaveBlocks()
    {
        $_this = $this;
        $blocksData = $this->request->getRequiredParam('blocks');
        $themeName = $this->request->getRequiredParam('theme');
        $layoutId = $this->request->getRequiredParam('layout');

        $layout = $this->layouts->getById($layoutId);

        if (!$layout->hasBlocks) {
            $layout->hasBlocks = 1;
        }

        $blocks = array_map(function ($blockData) use ($_this) {
            return $_this->blocks->create($blockData);
        }, $blocksData);
        $layout->blocks = $blocks;

        if (!$this->layouts->save($layout)) {
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
    public function actionInstall()
    {
        $this->layouts->install();
        $this->display->install();
        \Craft::$app->plugins->savePluginSettings(Themes::$plugin, ['installed' => true]);
        return [
            'message' => \Craft::t('themes', 'Layouts and display have been installed')
        ];
    }
}