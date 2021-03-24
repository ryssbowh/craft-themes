<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\ThemeEvent;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use craft\i18n\Locale;
use craft\models\Site;

class ThemesRules extends Service
{
    const CACHE_KEY_PREFIX = 'themes.rules.';

    /**
     * @var array
     */
    public $rules;

    /**
     * @var string
     */
    public $default;

    /**
     * Resolve the theme for the current request, 
     * get the theme either from cache or from defined theme rules
     * 
     * @return ?ThemeInterface
     */
    public function resolveCurrentTheme($event): ?ThemeInterface
    {
        $path = \Craft::$app->request->getFullPath();
        $currentSite = \Craft::$app->sites->getCurrentSite();
        $currentUrl = $currentSite->getBaseUrl().$path;
        $cached = $this->cacheService()->get(self::CACHE_KEY_PREFIX . $currentUrl);
        $theme = null;
        if (is_string($cached)) {
            $theme = $cached ? Themes::$plugin->registry->getTheme($cached) : null;
        } else {
            $themeName = $this->resolveRules($path, $currentSite, $currentUrl);
            $this->cacheService()->set(self::CACHE_KEY_PREFIX . $currentUrl, $themeName);
            if ($themeName) {
                $theme = Themes::$plugin->registry->getTheme($themeName);
            }
        }
        $this->themesRegistry()->setCurrent($theme);
        return $theme;
    }

    /**
     * Resolve all defined rules, returns theme name
     * 
     * @return ?string
     */
    protected function resolveRules(string $path, Site $site): ?string
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
        $trimmed = trim($ruleUrl, '/');
        if (substr($ruleUrl, 0, 1) == '/' and substr($ruleUrl, -1) == '/' and $ruleUrl != '/') {
            //Regular expression
            return preg_match($ruleUrl, $path);
        }
        return ($ruleUrl == '' or $trimmed == $path);
    }
}