<?php 

namespace Ryssbowh\CraftThemes\services;

use Detection\MobileDetect;
use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\ThemeEvent;
use Ryssbowh\CraftThemes\exceptions\ThemeException;
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
     * @var ?string
     */
    public $console;

    /**
     * @var boolean
     */
    public $setConsole;

    /**
     * @var boolean
     */
    public $cacheEnabled;

    /**
     * @var CacheInterface
     */
    public $cache;

    /**
     * @var MobileDetect
     */
    public $mobileDetect;

    /**
     * Resolve the theme for the current request, 
     * get the theme either from cache or from defined theme rules
     * 
     * @return ?ThemeInterface
     */
    public function resolveCurrentTheme(): ?ThemeInterface
    {
        if (\Craft::$app->request->getIsCpRequest()) {
            return null;
        }
        if (\Craft::$app->request->getIsConsoleRequest()) {
            if ($this->setConsole and $this->console) {
                try {
                    $theme = Themes::$plugin->registry->getTheme($this->console);
                } catch (ThemeException $e) {
                    return null;
                }
                $this->themesRegistry()->setCurrent($theme);
                return $theme;
            }
            return null;
        }
        $path = \Craft::$app->request->getFullPath();
        $currentSite = \Craft::$app->sites->getCurrentSite();
        $currentUrl = $currentSite->getBaseUrl().$path;
        $viewPort = $this->getViewPort();
        $cached = $this->getCache($currentUrl, $viewPort);
        $theme = null;
        if ($cached === null) {
            return null;
        }
        if (is_string($cached)) {
            $theme = Themes::$plugin->registry->getTheme($cached);
        } else {
            $themeName = $this->resolveRules($path, $currentSite, $viewPort);
            $this->setCache($currentUrl, $viewPort, $themeName);
            if ($themeName) {
                $theme = Themes::$plugin->registry->getTheme($themeName);
            }
        }
        if ($theme) {
            $this->themesRegistry()->setCurrent($theme);
        }
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
    protected function getCache(string $url, string $viewPort)
    {
        if (!$this->cacheEnabled) {
            return false;
        }
        $key = $this->cache->buildKey([self::CACHE_TAG, $url, $viewPort]);
        return $this->cache->get($key);
    }

    /**
     * Set the theme name cache
     * 
     * @param string $url
     * @param string $viewPort
     * @param string $themeName
     */
    protected function setCache(string $url, string $viewPort, ?string $themeName)
    {
        if (!$this->cacheEnabled) {
            return;
        }
        $key = $this->cache->buildKey([self::CACHE_TAG, $url, $viewPort]);
        $dep = new TagDependency([
            'tags' => [self::CACHE_TAG]
        ]);
        $this->cache->set($key, $themeName, null, $dep);
    }

    /**
     * Resolve all defined rules, returns theme name
     * 
     * @param  string $currentPath
     * @param  Site   $currentSite
     * @param  string $currentViewPort
     * @return ?string
     */
    protected function resolveRules(string $currentPath, Site $currentSite, string $currentViewPort): ?string
    {
        $themeName = null;
        foreach ($this->rules as $rule) {
            if (!$rule['enabled']) {
                continue;
            }
            $site = $language = $path = $viewPort = false;
            if ($site = $this->resolveSiteRule($rule['site'], $currentSite)) {
                if ($language = $this->resolveLanguageRule($rule['language'], $currentSite->getLocale())) {
                    if ($path = $this->resolvePathRule($rule['url'], $currentPath)) {
                        $viewPort = $this->resolveViewPortRule($rule['viewPort'], $currentViewPort);
                    }
                }
            }
            if ($site and $language and $path and $viewPort) {
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
     * Get user's view port
     * 
     * @return string
     */
    protected function getViewPort(): string
    {
        if ($this->mobileDetect->isMobile()) {
            return 'phone';
        }
        if ($this->mobileDetect->isTablet()) {
            return 'tablet';
        }
        return 'desktop';
    }

    /**
     * Resolves the site part of a rule
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
     * Resolves the language part of a rule
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
     * Resolves the view port part of a rule
     * 
     * @param  string $ruleViewPort
     * @param  string $viewPort
     * @return bool
     */
    protected function resolveViewPortRule(string $ruleViewPort, string $viewPort): bool
    {
        return ($ruleViewPort == '' or $viewPort == $ruleViewPort);
    }

    /**
     * Resolves the path part of a rule
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