<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\assets\LayoutsAssets;
use Ryssbowh\CraftThemes\models\layouts\Layout;

class CpLayoutsController extends Controller
{
    /**
     * Layouts index
     * 
     * @param  string|null $themeName
     * @param  int|null    $layout
     * @return Response
     */
    public function actionIndex(?string $themeName = null, ?int $layout = null)
    {
        $themes = $this->registry->getNonPartials(false, true);
        $theme = null;

        if ($themeName == null) {
            if (sizeof($themes)) {
                $keys = array_keys($themes);
                $themeName = $keys[0];
                $theme = $themes[$keys[0]];
            }
        } else {
            $theme = $this->registry->getTheme($themeName);
        }

        if ($layout == null) {
            if ($theme) {
                $layout = $this->layouts->getDefault($themeName);
            }
        } else {
            $layout = $this->layouts->getById($layout);
        }

        $this->view->registerAssetBundle(LayoutsAssets::class);

        // dd($this->layouts->getLayoutsByTheme(true));
        return $this->renderTemplate('themes/cp/layouts', [
            'title' => \Craft::t('themes', 'Layouts'),
            'themes' => $themes,
            'theme' => $themeName,
            'layout' => $layout ? $layout->id : null,
            'allLayouts' => $this->layouts->getLayoutsByTheme(true)
        ]);
    }

    /**
     * Delete a layout by id
     * 
     * @param  int    $id
     * @return Response
     */
    public function actionDelete(int $id)
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $layout = $this->layouts->getById($id);
        $layout->hasBlocks = 0;
        $this->layouts->save($layout);
        $this->blocks->deleteLayoutBlocks($layout);

        return $this->asJson([
            'message' => \Craft::t('themes', 'Layout deleted successfully.'),
            'layout' => $layout
        ]);
    }

    /**
     * Save blocks
     * 
     * @return Response
     */
    public function actionSave()
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();
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

        return $this->asJson([
            'message' => \Craft::t('themes', 'Blocks saved successfully.'),
            'blocks' => $blocks,
            'layout' => $layout
        ]);
    }
}