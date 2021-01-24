<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Theme;
use Ryssbowh\CraftThemes\exceptions\ThemeException;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;


class ThemesService
{	
	const CACHE_KEY = 'themes.registry';

	protected $themes;

	protected $folder;

	protected $current;

	public function __construct()
	{
		$this->folder = \Yii::getAlias('@themesPath');
		$this->themes = $this->buildThemes();
	}

	public function getCurrent(): ?ThemeInterface
	{
		$site = \Craft::$app->sites->getCurrentSite();
		$handle = Themes::$plugin->getSettings()->getHandle($site->uid);
		return $handle ? $this->getTheme($handle) : null;
	}

	public function getAll(): array
	{
		return $this->themes;
	}

	protected function getAsNames(): array
	{
		return array_map(function ($theme) {
			return $theme->getName();
		}, $this->getAll());
	}

	public function getTheme(string $handle): ThemeInterface
	{
		if (isset($this->getAll()[$handle])) {
			return $this->getAll()[$handle];
		}
		throw ThemeException::notDefined($handle);
	}

	public function getParent($theme): ?ThemeInterface
	{
		if (is_string($theme)) {
			$theme = $this->getTheme($theme);
		}
		if (!$extends = $theme->getExtends()) {
			return null;
		}
		return $this->getTheme($extends);
	}

	protected function buildThemes()
	{
		$themes = $this->getCache();
		if ($themes === null) {
			$themes = $this->scanThemes();
			$this->setCache($themes);
		}
		return $themes;
	}

	protected function scanThemes(): array
	{
		$dirs = array_filter(glob($this->folder . DIRECTORY_SEPARATOR . '*'), 'is_dir');
		$themes = [];
		foreach ($dirs as $dir) {
			$file = $dir . DIRECTORY_SEPARATOR . 'Theme.php';
			if (file_exists($file)) {
				if (preg_match('/^namespace\s+(.+?);/m', file_get_contents($file), $namespace)) {
					$class = $namespace[1].'\\Theme';
					$theme = new $class($dir);
					$themes[$theme->getHandle()] = $theme;
				}
			}
		}
		return $themes;
	}

	protected function getCache(): ?array
	{
		if (!\Craft::$app->cache->exists(self::CACHE_KEY)) {
			return null;
		}
		return \Craft::$app->cache->get(self::CACHE_KEY);
	}

	protected function setCache(array $themes)
	{
		return \Craft::$app->cache->set(self::CACHE_KEY, $themes);
	}
}