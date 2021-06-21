<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\ThemeEvent;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use craft\i18n\Locale;
use craft\models\Site;
use yii\caching\TagDependency;

class RulesService extends Service
{
    const CACHE_TAG = 'themes.rules';

    /**
     * @var array
     */
    public $rules;

    /**
     * @var string
     */
    public $default;

    /**
     * @var boolean
     */
    public $cacheEnabled;

    /**
     * @var CacheInterface
     */
    public $cache;

    /**
     * Resolve the theme for the current request, 
     * get the theme either from cache or from defined theme rules
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
        if ($cached === null) {
            return null;
        }
        if (is_string($cached)) {
            $theme = Themes::$plugin->registry->getTheme($cached);
        } else {
            $themeName = $this->resolveRules($path, $currentSite);
            $this->setCache($currentUrl, $themeName);
            if ($themeName) {
                $theme = Themes::$plugin->registry->getTheme($themeName);
            }
        }
        $this->themesRegistry()->setCurrent($theme);
        return $theme;
    }

    /**
     * Flush rules cache
     */
    public function flushCache()
    {
        TagDependency::invalidate($this->cache, self::CACHE_TAG);
    }

    /**
     * Get the cached theme name for a url
     * 
     * @param  string $url
     * @return ?string|false
     */
    protected function getCache(string $url)
    {
        if (!$this->cacheEnabled) {
            return false;
        }
        $key = $this->cache->buildKey([self::CACHE_TAG, $url]);
        return $this->cache->get($key);
    }

    /**
     * Set the theme name cache for an url
     * 
     * @param string $url
     * @param string $themeName
     */
    protected function setCache(string $url, ?string $themeName)
    {
        if (!$this->cacheEnabled) {
            return;
        }
        $key = $this->cache->buildKey([self::CACHE_TAG, $url]);
        $dep = new TagDependency([
            'tags' => [self::CACHE_TAG]
        ]);
        $this->cache->set($key, $themeName, null, $dep);
    }

    /**
     * Resolve all defined rules, returns theme name
     * 
     * @return ?string
     */
    protected function resolveRules(string $path, Site $currentSite): ?string
    {
        $themeName = null;
        foreach ($this->rules as $rule) {
            if (!$rule['enabled']) {
                continue;
            }
            $site = $language = $url = false;
            if ($site = $this->resolveSiteRule($rule['site'], $currentSite)) {
                if ($language = $this->resolveLanguageRule($rule['language'], $currentSite->getLocale())) {
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
        return $themeName;
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
        if (substr($ruleUrl, 0, 1) == '/' and substr($ruleUrl, -1) == '/' and $ruleUrl != '/') {
            //Regular expression
            return preg_match($ruleUrl, $path);
        }
        $trimmed = trim($ruleUrl, '/');
        return ($ruleUrl == '' or $trimmed == $path);
    }
}