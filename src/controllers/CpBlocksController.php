<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\assets\BlocksAssets;
use Ryssbowh\CraftThemes\models\Layout;
use craft\elements\Category;
use craft\elements\Entry;
use craft\web\Controller;

class CpBlocksController extends Controller
{
	protected $registry;
	protected $blockProviders;
	protected $blocks;
	protected $layouts;

	public function init()
	{
		parent::init();
		$this->registry = Themes::$plugin->registry;
		$this->blockProviders = Themes::$plugin->blockProviders;
		$this->blocks = Themes::$plugin->blocks;
		$this->layouts = Themes::$plugin->layouts;
	}

	public function actionIndex(?string $themeName = null)
	{
		$themes = $this->registry->getSelectables();
		if ($themeName == null and sizeof($themes)) {
			$keys = array_keys($themes);
			return $this->redirect('themes/blocks/' . $themes[$keys[0]]->getHandle());
		}
		$this->view->registerAssetBundle(BlocksAssets::class);
		return $this->renderTemplate('themes/blocks', [
			'title' => \Craft::t('themes', 'Theme Blocks'),
			'themes' => $themes,
			'selectedTheme' => $themeName ? $this->registry->getTheme($themeName) : null,
			'blockProviders' => $this->blockProviders->getAll(),
			'pages' => $this->getAvailablePages()
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

    protected function getAvailablePages(): array
    {
        $pages = Category::find()->where(['not', ['uri' => null]])->all();
        $pages = array_merge($pages, Entry::find()->where(['not', ['uri' => null]])->all());
        dd($pages);
        return $pages;
    }
}