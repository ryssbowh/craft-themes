<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\DisplayException;
use Ryssbowh\CraftThemes\exceptions\LayoutException;

class CpDisplayAjaxController extends Controller
{
    /**
     * @inheritDoc
     */
    public function beforeAction($action) 
    {
        $this->requirePermission('manageThemesDisplay');
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
     * Get view modes for a theme and a layout as json
     * 
     * @param  int $layout
     * @return array
     */
    public function actionViewModes(int $layout): array
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
     * @return array
     */
    public function actionDisplays(int $layout): array
    {
        $layout = $this->layouts->getById($layout);

        return [
            'displays' => $this->displays->getForLayout($layout),
        ];
    }

    /**
     * Validate field options
     * 
     * @return array
     */
    public function actionValidateFieldOptions(): array
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

    /**
     * Saves a layout
     * 
     * @return array
     */
    public function actionSaveLayout(): array
    {
        $displaysData = $this->request->getRequiredParam('displays');
        $layoutId = $this->request->getRequiredParam('layout');
        $viewModeData = $this->request->getRequiredParam('viewModes');
        $layout = $this->layouts->getById($layoutId);

        $viewModesMapping = [];
        $viewModes = [];
        foreach ($viewModeData as $data) {
            $data['layout_id'] = $layoutId;
            $viewMode = $this->viewModes->create($data);
            $viewModes[] = $viewMode;
            $viewModesMapping[$viewMode->handle] = $viewMode;
        }
        $layout->viewModes = $viewModes;

        $displays = [];
        foreach ($displaysData as $data) {
            $display = $this->displays->create($data);
            if (is_string($data['viewMode_id'])) {
                //When new view modes are created on frontend, viewMode_id will reference a view mode handle
                $display->viewMode = $viewModesMapping[$data['viewMode_id']];   
            }
            $displays[] = $display;
        }
        $layout->displays = $displays;

        if (!$this->layouts->save($layout)) {
            throw LayoutException::onSave();
        }

        return [
            'displays' => $layout->displays,
            'viewModes' => $layout->viewModes,
            'message' => \Craft::t('themes', 'Displays have been saved successfully')
        ];
    }
}