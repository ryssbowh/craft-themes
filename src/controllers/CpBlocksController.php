<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\assets\BlocksAssets;
use Ryssbowh\CraftThemes\events\RegisterBundles;
use Ryssbowh\CraftThemes\models\layouts\Layout;

/**
 * Controller for actions related to blocks
 */
class CpBlocksController extends Controller
{
    const REGISTER_ASSET_BUNDLES = 'register_asset_bundles';

    /**
     * Blocks index
     * 
     * @param  string|null $themeName
     * @param  int|null    $layout
     * @return Response
     */
    public function actionIndex(?string $themeName = null, ?int $layout = null)
    {
        $this->requirePermission('manageThemesBlocks');
        $themes = $this->registry->getNonPartials(false, true);
        $theme = null;
        $currentUser = \Craft::$app->getUser()->getIdentity();

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
        
        if ($this->hasEventHandlers(self::REGISTER_ASSET_BUNDLES)) {
            $event = new RegisterBundles;
            $this->trigger(self::REGISTER_ASSET_BUNDLES, $event);
            foreach ($event->bundles as $class) {
                $this->view->registerAssetBundle($class);
            }
        }
        $this->view->registerAssetBundle(BlocksAssets::class);

        return $this->renderTemplate('themes/cp/blocks', [
            'title' => \Craft::t('themes', 'Blocks'),
            'themes' => $themes,
            'theme' => $themeName,
            'layout' => $layout ? $layout->id : 0,
            'allLayouts' => $this->layouts->getBlockLayouts(),
            'cacheStrategies' => array_map(function ($strategy) {
                return $strategy->toArray();
            }, $this->blockCache->strategies),
            'showFieldHandles' => ($currentUser->admin && $currentUser->getPreference('showFieldHandles'))
        ]);
    }
}