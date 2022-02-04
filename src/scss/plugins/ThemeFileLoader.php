<?php
namespace Ryssbowh\CraftThemes\scss\plugins;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\ScssPhp\Compiler;
use Ryssbowh\ScssPhp\plugins\FileLoader;
use craft\helpers\StringHelper;

class ThemeFileLoader extends FileLoader
{
    /**
     * @var string|ThemeInterface
     */
    public $theme;

    /**
     * @inheritDoc
     */
    public function init(Compiler $compiler)
    {
        if (!$this->theme) {
            throw ThemeFileLoaderException("'theme' argument is required");
        }
        if (is_string($this->theme)) {
            $this->theme = Themes::$plugin->registry->getTheme($this->theme);
        }
        if (!$this->theme instanceof ThemeInterface) {
            throw ThemeFileLoaderException("'theme' argument must be a valid theme handle or ThemeInterface instance");
        }
        parent::init($compiler);
    }

    /**
     * Extract an asset
     * 
     * @param  string $path
     * @return ?string
     */
    public function extractAsset(string $path): ?string
    {
        if (!preg_match($this->test, $path)) {
            return null;
        }
        preg_match('/([^#\?]+)([#\?].+)?/', $path, $matches);
        $path = $newPath = $matches[1];
        $suffix = $matches[2] ?? '';
        if (isset($this->encoded[$path])) {
            return $this->encoded[$path];
        }
        //Trying the path in parent themes :
        $theme = $this->theme;
        while (!file_exists($newPath) and $theme) {
            if ($parent = $theme->parent) {
                $newPath = StringHelper::replaceBeginning($newPath, $theme->basePath, $parent->basePath);
            }
            $theme = $parent;
        }
        if (!file_exists($newPath)) {
            return null;
        }
        //Figuring out relative source folder :
        $theme = $this->theme;
        $srcFolder = $this->compiler->getSrcFolder();
        while (!StringHelper::startsWith($newPath, $srcFolder) and ($parent = $theme->parent)) {
            $srcFolder = StringHelper::replaceBeginning($srcFolder, $theme->basePath, $parent->basePath);
            $theme = $theme->parent;
        }
        $relativeSrcFolder = dirname(StringHelper::replaceBeginning($newPath, $srcFolder . DIRECTORY_SEPARATOR, '')) . DIRECTORY_SEPARATOR;
        return $this->_extractAsset($newPath, $suffix, $relativeSrcFolder);
    }
}