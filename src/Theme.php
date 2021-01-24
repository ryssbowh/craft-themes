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
	 * Template paths for this theme (including parent themes)
	 * 
	 * @var array
	 */
	protected $templatesPaths;

	/**
	 * Should parent assets be registered
	 * 
	 * @var boolean
	 */
	protected $registerParentAssets = true;

	/**
	 * Constructor
	 * 
	 * @param string $path
	 * @param array  $params
	 */
	public function __construct(string $path)
	{
		$this->basePath = $path;
	}

	/**
	 * Get theme path
	 * 
	 * @return string
	 */
	public function getPath(): string
	{
		return $this->basePath;
	}

	/**
	 * Get all template paths
	 * 
	 * @return array
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
	 * Get this theme's parent
	 * 
	 * @return ?ThemeInterface
	 */
	public function getParent(): ?ThemeInterface
	{
		return \Craft::$app->plugins->getPlugin('themes')->get('registry')->getParent($this);
	}

	/**
	 * Register all bundle assets in view.
	 */
	public function registerAssets()
	{
		if ($this->registerParentAssets and $parent = $this->getParent()) {
			$parent->registerAssets();
		}
		foreach ($this->getBundleAssets() as $asset) {
			\Craft::$app->view->registerAssetBundle($asset);
		}
	}
}