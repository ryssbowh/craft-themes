<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\assets\BlocksAssets;
use Ryssbowh\CraftThemes\models\Layout;
use craft\web\Controller;

class CpBlocksController extends Controller
{
	protected $registry;
	protected $blockProviders;
	protected $blocks;

	public function init()
	{
		parent::init();
		$this->registry = Themes::$plugin->registry;
		$this->blockProviders = Themes::$plugin->blockProviders;
		$this->blocks = Themes::$plugin->blocks;
	}

	public function actionIndex(?string $themeName = null)
	{
		$themes = $this->registry->getAll();
		if ($themeName == null and sizeof($themes)) {
			$keys = array_keys($themes);
			return $this->redirect('themes/blocks/' . $themes[$keys[0]]->getHandle());
		}
		$this->view->registerAssetBundle(BlocksAssets::class);
		return $this->renderTemplate('themes/blocks', [
			'title' => \Craft::t('themes', 'Theme Blocks'),
			'themes' => $themes,
			'selectedTheme' => $themeName ? $this->registry->getTheme($themeName) : null,
			'blockProviders' => $this->blockProviders->getAll()
		]);
	}

	public function actionSaveLayout(string $themeName)
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();
        $data = $this->request->getRequiredParam('regions');

        $layout = new Layout([
        	'theme' => $themeName
        ]);
        $layout->buildRegionsFromRawData($data);

        if (!$this->blocks->saveLayout($layout)) {
            return $this->asJson([
                'errors' => []
            ]);
        }
        
        return $this->asJson([
            'message' => \Craft::t('themes', 'Layout saved successfully.'),
            'layout' => $layout
        ]);
    }
}