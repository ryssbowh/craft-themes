<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\ThemeEvent;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use craft\base\Component;
use craft\i18n\Locale;
use craft\models\Site;

class ThemesRules extends Component
{
	const CACHE_KEY = 'themes.rules';

    const THEME_SET_EVENT = 'themes.set';

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

	/**
	 * inheritDoc
	 */
	public function init()
	{
		$this->cache = \Craft::$app->cache->get(self::CACHE_KEY) ?? [];
	}

	/**
	 * Resolve the theme for the current request, get the theme either from cache
     * or from defined theme rules
	 * 
	 * @return ?ThemeInterface
	 */
	public function resolveCurrentTheme(): ?ThemeInterface
	{
        $path = \Craft::$app->request->getFullPath();
        $currentSite = \Craft::$app->sites->getCurrentSite();
        $currentUrl = $currentSite->getBaseUrl().$path;
		$cached = $this->getCache($currentUrl);
        $theme = null;
		if (is_string($cached)) {
			$theme = $cached ? Themes::$plugin->registry->getTheme($cached) : null;
        } else {
            $themeName = $this->resolveRules($path, $currentSite, $currentUrl);
            if ($themeName) {
                $theme = Themes::$plugin->registry->getTheme($themeName);
            }
        }
		if ($theme) {
            $this->trigger(self::THEME_SET_EVENT, new ThemeEvent(['theme' => $theme]));
        }
		return $theme;
	}

	/**
	 * Clears rules cache
	 */
	public static function clearCaches()
	{
		\Craft::$app->cache->delete(self::CACHE_KEY);
	}

    /**
     * Resolve all defined rules, returns theme name
     * 
     * @return ?string
     */
    protected function resolveRules(string $path, Site $site, string $url): ?string
    {
        $themeName = null;
        foreach ($this->rules as $rule) {
            if (!$rule['enabled']) {
                continue;
            }
            $site = $language = $url = false;
            if ($site = $this->resolveSiteRule($rule['site'], $site)) {
                if ($language = $this->resolveLanguageRule($rule['language'], $site->getLocale())) {
                    $url = $this->resolvePathRule($rule['url'], $path);
                }
            }
            if ($site and $language and $url) {
                $themeName = $rule['theme'];
                break;
            }
        }
        if (!$themeName and $this->default) {
            $themeName = $this->default;
        }
        $this->setCache($url, $themeName);
        return $themeName;
    }

	/**
	 * Set the cache for the current url
	 * 
	 * @param string $url
	 * @param string $themeName
	 */
	protected function setCache(string $url, string $themeName)
	{
		$this->cache[$url] = $themeName;
		\Craft::$app->cache->set(self::CACHE_KEY, $this->cache);
	}

	/**
	 * Get the cache for the current url
	 * 
	 * @param  string $url
	 * @return ?string
	 */
	protected function getCache(string $url): ?string
	{
		return $this->cache[$url] ?? null;
	}

	/**
	 * Resolve the site part of a rule
	 * 
	 * @param  string $ruleSite
	 * @param  Site   $site
	 * @return bool
	 */
	protected function resolveSiteRule(string $ruleSite, Site $site): bool
	{
		return ($ruleSite == '' or $ruleSite == $site->uid);
	}

	/**
	 * Reolsves the language part of a rule
	 * 
	 * @param  string $ruleLanguage
	 * @param  Locale $locale
	 * @return bool
	 */
	protected function resolveLanguageRule(string $ruleLanguage, Locale $locale): bool
	{
		return ($ruleLanguage == '' or $ruleLanguage == $locale->id);
	}

	/**
	 * Resolve the path part of a rule
	 * 
	 * @param  string $ruleUrl
	 * @param  string $path
	 * @return bool
	 */
	protected function resolvePathRule(string $ruleUrl, string $path): bool
	{
		$trimmed = trim($ruleUrl, '/');
		if (substr($ruleUrl, 0, 1) == '/' and substr($ruleUrl, -1) == '/' and $ruleUrl != '/') {
			//Regular expression
			return preg_match($ruleUrl, $path);
		}
		return ($ruleUrl == '' or $trimmed == $path);
	}
}