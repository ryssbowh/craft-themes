<?php
namespace Ryssbowh\CraftThemes\console;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\scss\Compiler;
use ScssPhp\ScssPhp\OutputStyle;
use craft\console\Controller;
use yii\console\ExitCode;

class ScssController extends Controller
{   
    /**
     * @var string Sourcemaps : 'none', 'inline' or 'file'
     */
    public $sourcemaps;

    /**
     * @var string Css output style, 'expanded' or 'compressed'
     */
    public $style;

        /**
     * @var boolean Clean destination directory before compiling
     */
    public $cleanDestination;

    /**
     * @var string Destination file name format
     */
    public $fileName;

    /**
     * @var string Public folder. Aliases will be converted.
     */
    public $publicFolder;

    /**
     * @var string Hash method for hashing content and asset names. See https://www.php.net/manual/en/function.hash-algos.php
     */
    public $hashMethod;

    /**
     * @var boolean Disables compiler cache
     */
    public $disableCache;

    /**
     * @var boolean Force cache refresh
     */
    public $forceCacheRefresh;

    /**
     * @var integer Cache lifetime in seconds
     */
    public $cacheLifetime;

    /**
     * @var boolean Cache check import resolutions
     */
    public $cacheCheckImportResolutions;

    /**
     * @var string Caching folder. Aliases will be converted
     */
    public $cacheFolder;

    /**
     * @var array Aliases relative to the theme base path, separated by commas
     * "~:foo,#:bar" will convert into ['~' => 'foo', '#' => 'bar']
     * Those will be added to the theme compiler default aliases
     */
    public $aliases = [];

    /**
     * @var array Import paths, separated by commas. Aliases will be converted
     * Warning: the import paths will be prepended to the theme compiler default import paths
     */
    public $importPaths = [];

    /**
     * Compile a scss source file into a css destination file for a theme.
     * All options will override the theme's default compiler options.
     * 
     * @param  string $theme    Theme handle
     * @param  string $srcFile  Source file, relative to theme base path
     * @param  string $destFile Destination file, relative to public folder
     * @return int
     */
    public function actionCompile(string $theme, string $srcFile, string $destFile)
    {
        $theme = Themes::$plugin->registry->getTheme($theme);
        if (!$this->parseAliases()) {
            return ExitCode::CONFIG;
        }
        if (!$this->parseImportPaths()) {
            return ExitCode::CONFIG;
        }
        if ($this->cacheFolder) {
            $this->cacheFolder = \Craft::getAlias($this->cacheFolder);
        }
        if ($this->publicFolder) {
            $this->publicFolder = \Craft::getAlias($this->publicFolder);
        }
        $options = [];
        foreach ($this->compilerOptions() as $option) {
            if ($this->$option) {
                $options[$option] = $this->$option;
            }
        }
        $compiler = $theme->getScssCompiler($options);
        $compiler->compile([$srcFile => $destFile], $theme->basePath);
        $this->stdout(\Craft::t('themes', 'Compiler has run successfully') . "\n");
        return ExitCode::OK;

    }

    /**
     * @inheritDoc
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID), $this->compilerOptions());
    }

    /**
     * Options specific to the compiler
     * 
     * @return array
     */
    protected function compilerOptions(): array
    {
        return [
            'sourcemaps',
            'style',
            'cleanDestination',
            'fileName',
            'publicFolder',
            'hashMethod',
            'disableCache',
            'forceCacheRefresh',
            'cacheLifetime',
            'cacheFolder',
            'cacheCheckImportResolutions',
            'aliases',
            'importPaths'
        ];
    }

    /**
     * Parse the aliases, make sure they're valid
     * 
     * @return bool
     */
    protected function parseAliases(): bool
    {
        if ($this->aliases) {
            $aliases = [];
            foreach ($this->aliases as $alias) {
                $elems = explode(':', $alias);
                if (sizeof($elems) != 2) {
                    $this->stderr("The aliases argument is invalid\n");
                    return false;
                }
                $aliases[$elems[0]] = $elems[1];
            }
            $this->aliases = $aliases;
        }
        return true;
    }

    /**
     * Parse the import paths, resolve aliases and make sure folder exist
     * 
     * @return bool
     */
    protected function parseImportPaths(): bool
    {
        foreach ($this->importPaths as $index => $path) {
            $path = \Craft::getAlias($path);
            if (!is_dir($path)) {
                $this->stderr("The import path $path doesn't exist\n");
                return false;
            }
            $this->importPaths[$index] = $path;
        }
        return true;
    }
}