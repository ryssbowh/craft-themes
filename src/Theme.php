<?php 

namespace Ryssbowh\CraftThemes;

use Ryssbowh\CraftThemes\exceptions\ThemeException;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;

abstract class Theme implements ThemeInterface
{
	/**
	 * Base path for this theme
	 * @var string
	 */
	protected $basePath;

	/**
	 * Theme's handle
	 * @var string
	 */
	protected $handle;

	/**
	 * parent theme's handle
	 * @var ?string
	 */
	protected $extends;

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
	 * 		'*' => [
	 *   		CommonAssets::class
	 *   	],
	 *    	'blog' => [
	 *     		BlogAsset::class
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
	 * Constructor
	 * 
	 * @param string $path
	 */
	public function __construct(string $path, string $handle)
	{
		$this->basePath = $path;
		$this->handle = $handle;
	}

	/**
	 * inheritDoc
	 */
	public function getPath(): string
	{
		return $this->basePath;
	}

	/**
	 * inheritDoc
	 */
	public function getHandle(): string
	{
		return $this->handle;
	}

	/**
	 * inheritDoc
	 */
	public function getTemplatePath(): string
	{
		return 'templates';
	}

	/**
	 * inheritDoc
	 */
	public function getTemplatePaths(): array
	{
		if (!is_null($this->templatesPaths)) {
			return $this->templatesPaths;
		}
		$paths = [$this->basePath . DIRECTORY_SEPARATOR . $this->getTemplatePath()];
		if ($parent = $this->getParent()) {
			$paths = array_merge($paths, $parent->getTemplatePaths());
		}
		$this->templatesPaths = $paths;
		return $paths;
	}

	/**
	 * inheritDoc
	 */
	public function getParent(): ?ThemeInterface
	{
		if (!$this->extends) {
			return null;
		}
		return Themes::$plugin->themes->getTheme($this->extends);
	}

	/**
	 * inheritDoc
	 */
	public function hasParent(): bool
	{
		return ($this->getParent() !== null);
	}

	/**
	 * inheritDoc
	 */
	public function getAssetUrl(string $path): string
	{
		$fullPath = $this->getPath() . DIRECTORY_SEPARATOR . trim($path, DIRECTORY_SEPARATOR);
		if (file_exists($fullPath)) {
			return \Craft::$app->view->assetManager->getPublishedUrl($fullPath, true);
		}
		if ($this->inheritsAssets and $this->hasParent()) {
			return $this->getParent()->getAssetUrl($path);
		}
		return '';
	}

	/**
	 * inheritDoc
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