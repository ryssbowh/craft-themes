<?php
namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\DisplayException;
use Ryssbowh\CraftThemes\exceptions\LayoutException;

/**
 * Controller for ajax actions related to displays
 */
class CpDisplayAjaxController extends Controller
{
    /**
     * @inheritDoc
     */
    public function beforeAction($action) 
    {
        $this->requirePermission('manageThemesDisplays');
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
     * Validate field options
     * 
     * @return array
     */
    public function actionValidateFieldOptions(): array
    {
        $fieldId = $this->request->getBodyParam('fieldId');
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
}