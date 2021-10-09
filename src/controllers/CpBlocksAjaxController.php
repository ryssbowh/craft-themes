<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\DisplayException;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\Entry;

/**
 * Controller for ajax actions related to blocks
 */
class CpBlocksAjaxController extends Controller
{
    /**
     * @inheritDoc
     */
    public function beforeAction($action) 
    {
        $this->requirePermission('manageThemesBlocks');
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
     * Get all block providers as json
     * 
     * @return Response
     */
    public function actionBlockProviders()
    {
        return [
            'providers' => $this->blockProviders->all(true)
        ];
    }

    /**
     * Delete a layout by id
     * 
     * @param  int    $id
     * @return array
     */
    public function actionDeleteLayout(int $id): array
    {
        $layout = $this->layouts->getById($id);
        if ($layout->isCustom) {
            $this->layouts->deleteCustom($layout);
        } else {
            $layout->hasBlocks = false;
            $layout->blocks = [];
            $this->layouts->save($layout);
        }

        return [
            'message' => \Craft::t('themes', 'Layout deleted successfully.'),
            'layout' => $layout
        ];
    }

    /**
     * Get all blocks for a layout
     * 
     * @param  int    $layout
     * @return array
     */
    public function actionBlocks(int $layout): array
    {
        $layout = $this->layouts->getById($layout);
        return [
            'blocks' => $layout->blocks
        ];
    }

    /**
     * Save blocks
     * 
     * @return array
     */
    public function actionSaveBlocks(): array
    {
        $_this = $this;
        $blocksData = $this->request->getRequiredParam('blocks');
        $layoutId = $this->request->getParam('layout');
        $customData = $this->request->getParam('custom');
        if (!$layoutId) {
            $layout = $this->layouts->createCustom($customData);
        } else {
            $layout = $this->layouts->getById($layoutId);
        }

        if ($customData and $layout->type == LayoutService::CUSTOM_HANDLE) {
            $layout->name = $customData['name'];
            $layout->elementUid = $customData['elementUid'];
        }

        $blocks = array_map(function ($blockData) use ($_this, $layout) {
            $blockData['layout'] = $layout;
            return $_this->blocks->create($blockData);
        }, $blocksData);

        if (!$this->blocks->validateAll($blocks)) {
            $this->response->setStatusCode(400);
            $message = \Craft::t('themes', 'Error while saving blocks');
        } else {
            if (!$layout->hasBlocks) {
               $layout->hasBlocks = true;
            }
            $layout->blocks = $blocks;
            $this->layouts->save($layout, false);
            $message = \Craft::t('themes', 'Blocks saved successfully.');
        }

        return [
            'message' => $message,
            'blocks' => array_map(function ($block) {
                return $block->toArray();
            }, $blocks),
            'layout' => $layout
        ];
    }
}