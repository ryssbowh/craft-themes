<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\DisplayException;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\Entry;

class CpBlocksAjaxController extends Controller
{
    public function beforeAction($action) 
    {
        $this->requirePermission('manageThemesBlocks');
        $this->requireAcceptsJson();
        $this->requirePostRequest();
        return true;
    }

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
        $blocks = array_map(function ($blockData) use ($_this) {
            return $_this->blocks->create($blockData);
        }, $blocksData);

        if (!$this->blocks->validateAll($blocks)) {
            $this->response->setStatusCode(400);
            $message = \Craft::t('themes', 'Error while saving blocks');
        } else {
            $message = \Craft::t('themes', 'Blocks saved successfully.');
            if (!$layout->hasBlocks) {
               $layout->hasBlocks = 1;
            }
            $layout->blocks = $blocks;
            $this->layouts->save($layout, false);
        }

        return [
            'message' => $message,
            'blocks' => array_map(function ($block) {
                return $block->toArray();
            }, $blocks),
            'layout' => $layout
        ];
    }

    public function actionEntries(string $uid)
    {
        $entryTypes = array_values(array_filter(\Craft::$app->sections->getAllEntryTypes(), function ($entryType) use ($uid) {
            return $uid == $entryType->uid;
        }));
        $entries = array_map(function ($entry) {
            return [
                'uid' => $entry->uid,
                'title' => $entry->title
            ];
        }, Entry::find()->type($entryTypes[0])->all());
        usort($entries, function ($a, $b) {
            return ($a['title'] < $b['title']) ? -1 : 1;
        });
        return [
            'entries' => $entries
        ];
    }

    public function actionEntryViewModes(string $uid, string $theme)
    {
        $layout = Themes::$plugin->layouts->get($theme, LayoutService::ENTRY_HANDLE, $uid);
        return [
            'viewModes' => $layout->viewModes
        ];
    }
}