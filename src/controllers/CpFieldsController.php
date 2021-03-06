<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\services\ViewModeService;
use craft\base\Element;

class CpFieldsController extends Controller
{
    public function actionIndex(int $layout)
    {
        $this->requireAcceptsJson();
        $layout = $this->layouts->getById($layout);

        return $this->asJson([
            'fields' => $this->fields->getForLayout($layout),
        ]);
    }

    public function actionOptions()
    {
        $this->requireAcceptsJson();
        $fieldId = $this->request->getBodyParam('id');
        $displayerHandle = $this->request->getRequiredParam('displayer');

        if ($fieldId) {
            $field = $this->fields->getById($fieldId);
            $field->displayerHandle = $displayerHandle;
            $displayer = $field->displayer;
        } else {
            $displayer = $this->fields->getDisplayerByHandle($displayerHandle);
        }
        return $this->asJson([
            'html' => $displayer->getOptionsHtml()
        ]);
    }

    public function actionSave()
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $fieldsData = $this->request->getRequiredParam('fields');
        $layoutId = $this->request->getRequiredParam('layout');
        $viewModeData = $this->request->getRequiredParam('viewModes');

        $viewModeMapping = [];
        $viewModes = [];
        foreach ($viewModeData as $data) {
            $data['layout'] = $layoutId;
            $viewMode = $this->viewModes->fromData($data);
            $this->viewModes->save($viewMode);
            $viewModeMapping[$viewMode->handle] = $viewMode->id;
            $viewModes[] = $viewMode;
        }

        $fields = $fieldIds = [];
        foreach ($fieldsData as $data) {
            if (is_string($data['viewMode'])) {
                $data['viewMode'] = $viewModeMapping[$data['viewMode']];
            }
            $field = $this->fields->fromData($data);
            $this->fields->save($field, true);
            $fields[] = $field;
            $fieldIds[] = $field->id;
        }

        $this->viewModes->deleteForLayout(array_values($viewModeMapping), $layoutId);
        $this->fields->deleteForLayout($fieldIds, $layoutId);

        return $this->asJson([
            'fields' => $fields,
            'viewModes' => $viewModes,
            'message' => \Craft::t('themes', 'Fields have been saved successfully')
        ]);
    }
}