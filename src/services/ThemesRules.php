<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use craft\base\Component;

class ThemesRules extends Component
{
	const CACHE_KEY = 'themes.rules';

	/**
	 * @var array
	 */
	public $rules;

	/**
	 * @var string
	 */
	public $default;

	/**
	 * @var array
	 */
	public $cache;

	public function init()
	{
		$this->cache = \Craft::$app->cache->get(self::CACHE_KEY) ?? [];
	}

	public function resolveCurrentTheme(): ?ThemeInterface
	{
		$path = \Craft::$app->request->getFullPath();
		$url = $site->getBaseUrl().'/'.$path;
		$themeName = $this->getCache($url);
		if (is_string($themeName)) {
			return $themeName ? Themes::$plugin->registry->getTheme($themeName) : null;
		}
		$site = \Craft::$app->sites->getCurrentSite();
		$themeName = '';
		foreach ($this->rules as $rule) {
			if (!$rule['enabled']) {
				continue;
			}
			switch ($rule['type']) {
				case 'site':
					$themeName = $this->resolveSiteRule($rule, $site);
					break;
				case 'language':
					$themeName = $this->resolveLanguageRule($rule, $site->getLocale());
					break;
				default:
					$themeName = $this->resolveUrlRule($rule, $url, $path);
			}
			if ($themeName) {
				break;
			}
		}
		if (!$themeName and $this->default) {
			$themeName = $this->default;
		}
		$this->setCache($url, $themeName);
		return $themeName ? Themes::$plugin->registry->getTheme($themeName) : null;
	}

	public static function clearCache()
	{
		$this->cache = [];
		\Craft::$app->cache->delete(self::CACHE_KEY);
	}

	protected function setCache(string $url, string $themeName)
	{
		$this->cache[$url] = $themeName;
		\Craft::$app->cache->set(self::CACHE_KEY, $this->cache);
	}

	protected function getCache(string $url): ?string
	{
		return $this->cache[$url] ?? null;
	}

	protected function resolveSiteRule(array $rule, Site $site): string;
	{
		return ($rule['site'] == $site->uid) ? $rule['theme'] : '';
	}

	protected function resolveLanguageRule(array $rule, Locale $locale): string;
	{
		return ($rule['language'] == $locale->id) ? $rule['theme'] : '';
	}

	protected function resolveUrlRule(array $rule, string $fullUrl, string $path): string;
	{
		$match = false;
		$ruleUrl = trim($rule['url'], '/');
		if (substr($ruleUrl, 0, 4) == 'http') {
			if ($ruleUrl == $fullUrl) {
				$match = true;
			}
		} else if (substr($rule['url'], 0, 1) == '/' and substr($rule['url'], -1) == '/') {

		} else if ($ruleUrl == $path) {
			$match = true;
		}
		return $match ? $rule['theme'] : '';
	}
}