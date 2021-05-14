<?php 

namespace Ryssbowh\CraftThemes;

use Ryssbowh\CraftThemes\exceptions\ThemeException;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\models\PageLayout;
use craft\base\Plugin;
use yii\base\ArrayableTrait;

abstract class ThemePlugin extends Plugin implements ThemeInterface
{
    use ArrayableTrait;
    
    /**
     * array of all the template paths (including those of the parents)
     * @var array
     */
    protected $templatesPaths;

    /**
     * Should the parent asset bundle be registered as well
     * @var boolean
     */
    protected $inheritsAssetBundles = true;

    /**
     * Bundle assets defined by this theme, keyed by the url path. '*' for all paths :
     * [
     *      '*' => [
     *          CommonAssets::class
     *      ],
     *      'blog' => [
     *          BlogAsset::class
     *      ]
     * ]
     * @var array
     */
    protected $assetBundles = [];

    /**
     * Should this theme inherits parent's assets
     * @var boolean
     */
    protected $inheritsAssets = true;

    /**
     * @inheritDoc
     */
    public function getTemplatesFolder(): string
    {
        return 'templates';
    }

    /**
     * @inheritDoc
     */
    public function getTemplatePaths(): array
    {
        if (!is_null($this->templatesPaths)) {
            return $this->templatesPaths;
        }
        $paths = [$this->getBasePath() . DIRECTORY_SEPARATOR . $this->getTemplatesFolder()];
        if ($parent = $this->getParent()) {
            $paths = array_merge($paths, $parent->getTemplatePaths());
        }
        $this->templatesPaths = $paths;
        return $paths;
    }

    /**
     * @inheritDoc
     */
    public function isPartial(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getExtends(): ?string
    {
        return null;
    }

    public function contentBlockRegion(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getParent(): ?ThemeInterface
    {
        if (!$this->extends) {
            return null;
        }
        return Themes::$plugin->registry->getTheme($this->getExtends());
    }

    /**
     * @inheritDoc
     */
    public function getAssetUrl(string $path): string
    {
        $fullPath = $this->getBasePath() . DIRECTORY_SEPARATOR . trim($path, DIRECTORY_SEPARATOR);
        if (file_exists($fullPath)) {
            return \Craft::$app->view->assetManager->getPublishedUrl($fullPath, true);
        }
        if ($this->inheritsAssets and $this->getExtends() !== null) {
            return $this->getParent()->getAssetUrl($path);
        }
        return '';
    }

    /**
     * @inheritDoc
     */
    public function registerAssetBundles(string $urlPath)
    {
        if ($this->inheritsAssetBundles and $parent = $this->getParent()) {
            $parent->registerAssetBundles($urlPath);
        }
        foreach ($this->getAssetBundles($urlPath) as $asset) {
            \Craft::$app->view->registerAssetBundle($asset);
        }
    }

    /**
     * Get layout for this theme
     * 
     * @return Layout
     */
    public function getLayout(): Layout
    {
        return Themes::$plugin->layouts->getLayout($this);
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return ['name', 'handle', 'regions'];
    }

    /**
     * @inheritDoc
     */
    public function getRegions(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function afterSet()
    {

    }

    public function getPreviewImage(): string
    {
        $file = glob($this->basePath . "/preview.{png,svg,jpeg,jpg}", GLOB_BRACE)[0] ?? null;
        if (!$file) {
            $file = \Yii::getAlias('@Ryssbowh/CraftThemes/assets/no-preview.png');
        }
        return \Craft::$app->view->assetManager->getPublishedUrl($file, true);
    }

    /**
     * Get bundle assets for a url path
     * 
     * @param  string $urlPath
     * @return array
     */
    protected function getAssetBundles(string $urlPath): array
    {
        return array_merge($this->assetBundles['*'] ?? [], $this->assetBundles[$urlPath] ?? []);
    }
}