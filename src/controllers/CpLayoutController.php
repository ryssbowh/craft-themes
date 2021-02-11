<?php 

namespace Ryssbowh\CraftThemes\controllers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\assets\BlocksAssets;
use Ryssbowh\CraftThemes\models\Block;
use Ryssbowh\CraftThemes\models\Layout;
use craft\db\Table;
use craft\elements\Category;
use craft\elements\Entry;
use craft\web\Controller;

class CpLayoutController extends Controller
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
		$themes = $this->registry->getSelectables(true);
		if ($themeName == null and sizeof($themes)) {
			$keys = array_keys($themes);
			return $this->redirect('themes/blocks/' . $themes[$keys[0]]['handle']);
		}
		$this->view->registerAssetBundle(BlocksAssets::class);
        $theme = $themeName ? $this->registry->getTheme($themeName) : null;
        // dd(Themes::$plugin->blocks->getForTheme($theme));
		return $this->renderTemplate('themes/cp/blocks', [
			'title' => \Craft::t('themes', 'Theme Blocks'),
			'themes' => $themes,
			'theme' => $theme->toArray(),
            'blocks' => $theme ? $this->blocks->getForTheme($theme) : null,
            'regions' => $theme ? $theme->getRegions() : null,
			'providers' => $this->blockProviders->getAll(true),
			'pages' => $this->getAvailablePages()
		]);
	}

	public function actionSave(string $themeName)
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();
        $_this = $this;
        $data = $this->request->getRequiredParam('blocks');

        $blocks = array_map(function ($blockData) use ($_this) {
            return $_this->blocks->fromData($blockData);
        }, $data);

        if (!$this->blocks->saveBlocks($blocks, $themeName)) {
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
            'blocks' => $blocks
        ]);
    }

    protected function getAvailablePages(): array
    {
    	$pages = [];
        $categories = Category::find()->where(['not', ['uri' => null]])->all();
        foreach ($categories as $category) {
            $pages['categories'][$category->getGroup()->name][] = $category;
        }
        $entries = Entry::find()->where(['not', ['uri' => null]])->all();
        foreach ($entries as $entry) {
        	$pages['entries'][$entry->getSection()->type][$entry->getSection()->name][] = $entry;
        }
        $routes = \Craft::$app->routes;
        $pages['routes'] = $routes->getProjectConfigRoutes();
        // dd($pages);
        return $pages;
    }
}