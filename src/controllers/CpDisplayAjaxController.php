<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\DisplayException;
use Ryssbowh\CraftThemes\exceptions\LayoutException;

class CpDisplayAjaxController extends Controller
{
    public function beforeAction($action) 
    {
        $this->requirePermission('manageThemesDisplay');
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
            'viewModes' => $this->viewModes->getForLayout($layout)
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

        $viewModes = [];
        foreach ($viewModeData as $data) {
            $data['layout_id'] = $layoutId;
            $viewModes[] = $this->viewModes->create($data);
        }
        $layout->viewModes = $viewModes;
        if (!$this->layouts->save($layout)) {
            throw LayoutException::onSave();
        }

        $viewModeMapping = [];
        foreach ($layout->viewModes as $viewMode) {
            $viewModeMapping[$viewMode->handle] = $viewMode->id;
        }

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
}