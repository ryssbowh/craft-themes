<?php
namespace Ryssbowh\CraftThemes\base;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\ScssCompilerEvent;
use Ryssbowh\CraftThemes\exceptions\ThemeException;
use Ryssbowh\CraftThemes\helpers\ProjectConfigHelper;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\interfaces\ThemePreferencesInterface;
use Ryssbowh\CraftThemes\scss\ScssLogger;
use Ryssbowh\CraftThemes\scss\plugins\ThemeFileLoader;
use Ryssbowh\ScssPhp\Compiler;
use Ryssbowh\ScssPhp\plugins\JsonManifest;
use craft\base\Plugin;
use yii\base\ArrayableTrait;

/**
 * Base class for all themes
 */
abstract class ThemePlugin extends Plugin implements ThemeInterface
{
    use ArrayableTrait;

    const DEFINE_SCSS_COMPILER = 'define_scss_compiler';

    /**
     * @var Compiler
     */
    protected $_compiler;

    /**
     * @var array
     */
    protected $_regions;
    
    /**
     * array of all the template paths (including those of the parents)
     * @var string[]
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
     *      '*' => [
     *          CommonAssets::class
     *      ],
     *      'blog' => [
     *          BlogAsset::class
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
     * @var ThemePreferencesInterface
     */
    protected $_preferences;

    /**
     * @inheritDoc
     */
    public function getTemplatesFolder(): string
    {
        return 'templates';
    }

    /**
     * @inheritDoc
     */
    public function getTemplatePaths(): array
    {
        if (!is_null($this->templatesPaths)) {
            return $this->templatesPaths;
        }
        $paths = [$this->getBasePath() . DIRECTORY_SEPARATOR . $this->getTemplatesFolder()];
        if ($parent = $this->getParent()) {
            $paths = array_merge($paths, $parent->getTemplatePaths());
        }
        $this->templatesPaths = $paths;
        return $paths;
    }

    /**
     * @inheritDoc
     */
    public function isPartial(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getExtends(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getParent(): ?ThemeInterface
    {
        if (!$this->extends) {
            return null;
        }
        return Themes::$plugin->registry->getTheme($this->getExtends());
    }

    /**
     * @inheritDoc
     */
    public function getAssetUrl(string $path): string
    {
        $fullPath = $this->getBasePath() . DIRECTORY_SEPARATOR . trim($path, DIRECTORY_SEPARATOR);
        if (file_exists($fullPath)) {
            return \Craft::$app->view->assetManager->getPublishedUrl($fullPath, true);
        }
        if ($this->inheritsAssets and $this->getExtends() !== null) {
            return $this->getParent()->getAssetUrl($path);
        }
        return '';
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getPreferences(): ThemePreferencesInterface
    {
        if (!$this->_preferences) {
            $this->_preferences = $this->getPreferencesModel();
        }
        return $this->_preferences;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return ['name', 'handle', 'regions'];
    }

    /**
     * @inheritDoc
     */
    public function getRegions(): array
    {
        if (is_null($this->_regions)) {
            $regionsArray = $this->defineRegions();
            if (is_null($regionsArray)) {
                if ($parent = $this->parent) {
                    return $parent->regions;
                }
            }
            $defined = [];
            $this->_regions = [];
            foreach ($regionsArray ?? [] as $region) {
                if (!isset($region['handle'])) {
                    throw ThemeException::regionParameterMissing('handle', $this->handle);
                }
                if (!isset($region['name'])) {
                    throw ThemeException::regionParameterMissing('name', $this->handle);
                }
                if (in_array($region['handle'], $defined)) {
                    throw ThemeException::duplicatedRegion($this->handle, $region['handle']);
                }
                $defined[] = $region['handle'];
                $this->_regions[] = $region;
            }
        }
        return $this->_regions;
    }

    /**
     * @inheritDoc
     */
    public function afterThemeUninstall()
    {
    }

    /**
     * @inheritDoc
     */
    public function afterThemeInstall()
    {
    }
    
    /**
     * @inheritDoc
     */
    public function hasDataInstalled(): bool
    {
        return ProjectConfigHelper::isDataInstalledForTheme($this);
    }

    /**
     * @inheritDoc
     */
    public function afterSet()
    {
    }

    /**
     * @inheritDoc
     */
    public function getHasPreview(): bool
    {
        return !is_null($this->getPreviewImagePath());
    }

    /**
     * @inheritDoc
     */
    public function getPreviewImage(): string
    {
        $file = $this->getPreviewImagePath();
        if (!$file) {
            $file = \Yii::getAlias('@Ryssbowh/CraftThemes/assets/images/no-preview.png');
        }
        return \Craft::$app->view->assetManager->getPublishedUrl($file, true);
    }

    /**
     * @inheritDoc
     */
    public function getRegionsTemplate(): string
    {
        return 'regions';
    }

    /**
     * @inheritDoc
     */
    public function getScssCompiler(array $options = []): Compiler
    {
        if ($this->_compiler !== null) {
            return $this->_compiler;
        }
        $defaultOptions = $this->getScssCompilerOptions();
        //Sorting import paths, start with the ones in the $options argument
        //then add the default ones
        //then add the parent themes to it to keep inheritance in imports
        $importPaths = array_merge($options['importPaths'] ?? [], $defaultOptions['importPaths'] ?? []);
        $parent = $this->parent;
        while ($parent) {
            $importPaths[] = $parent->basePath;
            $parent = $parent->parent;
        }
        //Sorting aliases, the ones from the $options argument will overridde
        $aliases = array_merge($defaultOptions['aliases'] ?? [], $options['aliases'] ?? []);
        $options = array_merge($defaultOptions, $options, [
            'aliases' => $aliases,
            'importPaths' => $importPaths
        ]);
        $compiler = new Compiler(
            $options,
            $this->getScssCompilerPlugins(),
            new ScssLogger
        );
        $this->trigger(self::DEFINE_SCSS_COMPILER, new ScssCompilerEvent([
            'compiler' => $compiler
        ]));
        return $this->_compiler = $compiler;
    }

    /**
     * Get scss compiler default options
     * 
     * @return array
     */
    protected function getScssCompilerOptions(): array
    {
        $devMode = \Craft::$app->getConfig()->getGeneral()->devMode;
        return [
            'publicFolder' => \Craft::getAlias('@themesWebPath/' . $this->handle),
            'style' => $devMode ? 'expanded' : 'minified',
            'sourcemaps' => $devMode ? 'inline' : 'none',
            'aliases' => ['~' => 'node_modules']
        ];
    }

    /**
     * Get the plugins for the scss compiler
     * 
     * @return array
     */
    protected function getScssCompilerPlugins(): array
    {
        return [
            new JsonManifest,
            new ThemeFileLoader([
                'test' => '/.+.(?:ico|jpg|jpeg|png|gif)([\?#].*)?$/',
                'theme' => $this
            ]),
            new ThemeFileLoader([
                'test' => '/.+.svg([\?#].*)?$/',
                'mimetype' => 'image/svg+xml',
                'theme' => $this
            ]),
            new ThemeFileLoader([
                'test' => '/.+.ttf([\?#].*)?$/',
                'mimetype' => 'application/octet-stream',
                'theme' => $this
            ]),
            new ThemeFileLoader([
                'test' => '/.+.woff([\?#].*)?$/',
                'mimetype' => 'application/font-woff',
                'theme' => $this
            ]),
            new ThemeFileLoader([
                'test' => '/.+.woff2([\?#].*)?$/',
                'mimetype' => 'application/font-woff',
                'theme' => $this
            ]),
            new ThemeFileLoader([
                'test' => '/.+.eot([\?#].*)?$/',
                'theme' => $this
            ]),
        ];
    }

    /**
     * Get theme preferences model
     * 
     * @return ThemePreferencesInterface
     */
    protected function getPreferencesModel(): ThemePreferencesInterface
    {
        return new ThemePreferences;
    }

    /**
     * Get the path of the image preview file
     * 
     * @return ?string
     */
    protected function getPreviewImagePath(): ?string
    {
        return glob($this->basePath . "/preview.{png,svg,jpeg,jpg}", GLOB_BRACE)[0] ?? null;
    }

    /**
     * Get bundle assets for a url path
     * 
     * @param  string $urlPath
     * @return string[]
     */
    protected function getAssetBundles(string $urlPath): array
    {
        $pathBundles = [];
        foreach ($this->assetBundles as $path => $bundles) {
            if (substr($path, 0, 1) == '/' and substr($path, -1, 1) == '/' and $path != '/') {
                if (preg_match($path, $urlPath)) {
                    $pathBundles = array_merge($pathBundles, $bundles);
                }
            } else if ($path == $urlPath) {
                $pathBundles = array_merge($pathBundles, $bundles);
            }
        }
        return array_merge($this->assetBundles['*'] ?? [], $pathBundles);
    }

    /**
     * Define regions as array
     *
     * [
     *     [
     *         'handle' => 'region-handle',
     *         'name' => 'Region',
     *         'width' => '100%' //Used in backend blocks section
     *     ]
     * ]
     *
     * Returning null will load parent's theme regions
     */
    protected function defineRegions(): ?array
    {
        return null;
    }
}