<?php
namespace Ryssbowh\CraftThemes\scss;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\ScssBundleException;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\ScssPhp\Compiler;
use craft\web\AssetBundle;

abstract class ScssAssetBundle extends AssetBundle
{
    /**
     * @var string
     */
    public $theme;

    /**
     * @var array Scss files
     * This must be an associative array :
     * [
     *     'relative/to/theme/base/path' => 'relative/to/public/path'
     * ]
     */
    public $scssFiles = [];

    /**
     * Overridde any compiler options here
     * @var array
     */
    public $compilerOptions = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        if (!$this->theme) {
            throw ScssBundleException::noTheme(get_class($this));
        }
        if (!Themes::$plugin->registry->hasTheme($this->theme)) {
            throw ScssBundleException::themeUndefined(get_class($this), $this->theme);
        }
    }

    /**
     * @inheritDoc
     */
    public function publish($am)
    {
        $this->compileScssFiles();
        parent::publish($am);
    }

    /**
     * Compile the scss files
     */
    protected function compileScssFiles()
    {
        if (!$this->isCompilingEnabled()) {
            return;
        }
        $this->getCompiler()->compile($this->scssFiles, $this->getTheme()->basePath);
    }

    /**
     * Get the compiler
     * 
     * @return Compiler
     */
    protected function getCompiler(): Compiler
    {
        return $this->getTheme()->getScssCompiler($this->compilerOptions);
    }

    /**
     * Should the compiling be run, true if devMode is on
     * 
     * @return boolean
     */
    protected function isCompilingEnabled(): bool
    {
        return \Craft::$app->getConfig()->getGeneral()->devMode;
    }

    /**
     * Get the theme plugin instance
     * 
     * @return ThemeInterface
     */
    protected function getTheme(): ThemeInterface
    {
        return Themes::$plugin->registry->getTheme($this->theme);
    }
}