<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Theme;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\ThemeException;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use craft\base\Component;
use craft\models\Site;


class ThemesRegistry extends Component
{	
	const CACHE_FILE = '@vendor/ryssbowh/craft-themes/themes.php';

	/**
	 * @var ?array
	 */
	protected $themes;

	/**
	 * @var string
	 */
	public $folder;

	/**
	 * @var null|ThemeInterface
	 */
	protected $currentTheme;

	public function init()
	{
		$this->themes = $this->buildThemes();
	}

	/**
	 * Get cache file path
	 * 
	 * @return string
	 */
	public static function getCachePath(): string
	{
		return \Craft::getAlias(self::CACHE_FILE);
	}

	/**
	 * Clear themes cache
	 */
	public static function clearCaches()
	{
		if (self::hasCache()) {
			unlink(self::getCachePath());
		}
	}

	/**
	 * Does theme cache exists
	 * 
	 * @return boolean
	 */
	public static function hasCache(): bool
	{
		return file_exists(self::getCachePath());
	}

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
	 * Set current theme
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
		if ($set) {
			\Yii::setAlias('@themePath', '@root/themes/' . $this->currentTheme->getHandle());
        	\Yii::setAlias('@themeWebPath', '@webroot/themes/' . $this->currentTheme->getHandle());
        	\Craft::info("Theme has been set to : ".$this->currentTheme->getName(), __METHOD__);
		} else {
			\Craft::info("No theme has been set for the current request", __METHOD__);
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
			return $theme->getName();
		}, $this->getAll());
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

	/**
	 * Get all themes cache
	 * 
	 * @return ?array
	 */
	protected function getCache(): ?array
	{
		if (!$this->hasCache()) {
			return null;
		}
		$cached = require $this->getCachePath();
		$themes = [];
		foreach ($cached as $array) {
			$class = $array['class'];
			$themes[$array['handle']] = new $class($array['dir'], $array['handle']);
		}
		return $themes;
	}

	/**
	 * Saves themes in cache
	 * 
	 * @param array $themes
	 */
	protected function setCache(array $themes)
	{
		$cache = [];
		foreach ($themes as $theme) {
			$cache[] = [
				'class' => get_class($theme),
				'dir' => $theme->getPath(),
				'handle' => $theme->getHandle()
			];
		}
		$str = "<?php\n\nreturn ".var_export($cache, true).";";
		file_put_contents($this->getCachePath(), $str);
	}

	/**
	 * Build all themes classes either from cache or from the disk. 
	 * Will save the result in cache.
	 * 
	 * @return array
	 */
	protected function buildThemes(): array
	{
		$themes = $this->getCache();
		if ($themes === null) {
			$themes = $this->scanThemes();
			$this->setCache($themes);
		}
		return $themes;
	}

	/**
	 * Scan all themes from the disk
	 * 
	 * @return array
	 */
	protected function scanThemes(): array
	{
		$dirs = array_filter(glob($this->folder . DIRECTORY_SEPARATOR . '*'), 'is_dir');
		$themes = [];
		foreach ($dirs as $dir) {
			$file = $dir . DIRECTORY_SEPARATOR . 'Theme.php';
			if (file_exists($file)) {
				if (preg_match('/^namespace\s+(.+?);/m', file_get_contents($file), $namespace)) {
					$handle = basename($dir);
					$class = $namespace[1].'\\Theme';
					$theme = new $class($dir, $handle);
					$themes[$handle] = $theme;
				}
			}
		}
		return $themes;
	}
}