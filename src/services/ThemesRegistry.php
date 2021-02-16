<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Theme;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\ThemeException;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use craft\base\Component;
use craft\base\PluginInterface;
use craft\models\Site;


class ThemesRegistry extends Component
{	
	/**
	 * @var ?array
	 */
	protected $themes;

	/**
	 * @var null|ThemeInterface
	 */
	protected $currentTheme;

	/**
	 * Get theme for the current site
	 * 
	 * @return ?ThemeInterface
	 */
	public function getCurrent(): ?ThemeInterface
	{
		return $this->currentTheme;
	}

	/**
	 * Sets current theme
	 * 
	 * @param  string|ThemeInterface|null $theme
	 * @return ?ThemeInterface
	 */
	public function setCurrent($theme): ?ThemeInterface
	{
		$set = false;
		if (is_string($theme)) {
			$this->currentTheme = $this->getTheme($theme);
			$set = true;
		} elseif ($theme instanceof ThemeInterface) {
			$this->currentTheme = $theme;
			$set = true;
		} elseif ($theme === null) {
			$this->currentTheme = null;
		} 
		return $this->currentTheme;
	}

	/**
	 * Get all themes
	 * 
	 * @return array
	 */
	public function getAll(): array
	{
		if ($this->themes === null) {
			$this->loadThemes();
		}
		return $this->themes;
	}

	/**
	 * Get all themes as names
	 * 
	 * @return array
	 */
	public function getAsNames(): array
	{
		return array_map(function ($theme) {
			return $theme->name;
		}, $this->getAll());
	}

	/**
	 * Get all non partial themes
	 * 
	 * @param  boolean $asNames
	 * @return array
	 */
	public function getNonPartials(bool $asNames = false): array
	{
		$themes = array_filter($this->getAll(), function ($theme) {
			return !$theme->isPartial();
		});
		if ($asNames) {
			return array_map(function ($theme) {
				return $theme->name;
			}, $themes);
		}
		return $themes;
	}

	/**
	 * Get a theme by handle
	 * 
	 * @param  string $handle
	 * @throws ThemeException
	 * @return ThemeInterface
	 */
	public function getTheme(string $handle): ThemeInterface
	{
		if (isset($this->getAll()[$handle])) {
			return $this->getAll()[$handle];
		}
		throw ThemeException::notDefined($handle);
	}

	protected function loadThemes()
	{
		$themes = [];
		$plugins = \Craft::$app->plugins->getAllPlugins();
		foreach ($plugins as $plugin) {
			if ($plugin instanceof ThemeInterface) {
				$themes[$plugin->getHandle()] = $plugin;
			}
		}
		$this->themes = $themes;
	}
}