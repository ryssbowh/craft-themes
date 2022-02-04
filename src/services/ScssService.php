<?php
namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\exceptions\InlineScssException;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\scss\ScssLogger;
use Ryssbowh\ScssPhp\Compiler;
use Ryssbowh\ScssPhp\plugins\FileLoader;
use ScssPhp\ScssPhp\OutputStyle;
use craft\helpers\FileHelper;
use craft\helpers\StringHelper;

class ScssService extends Service
{   
    const CSS_DEST_FOLDER = '@storage/runtime/scss/dest';
    const CSS_SRC_FOLDER = '@storage/runtime/scss/src';
    const DEST_FILE = 'compiled.css';

    /**
     * Compile some scss defined in a template, identified by a hash
     * then registers the css on the view
     * 
     * @param  string         $scss
     * @param  string         $hash
     * @param  ThemeInterface $theme
     * @param  string         $template
     * @param  array          $options
     * @param  bool           $force
     */
    public function compileInlineScss(string $scss, string $hash, ThemeInterface $theme, string $template, array $options = [], bool $force = false)
    {
        $scssFile = $this->getSrcFile($hash);
        $cssFile = $this->getDestFile($hash);
        $scssFileExists = file_exists($scssFile);
        $cssFileExists = file_exists($cssFile);
        if ($force or !$scssFileExists or !$cssFileExists or ($scssFileExists and filemtime($template) > filemtime($scssFile))) {
            FileHelper::createDirectory(dirname($scssFile));
            file_put_contents($scssFile, $scss);
            $compiler = $theme->getScssCompiler($options);
            $compiler->publicFolder = dirname($cssFile);
            $compiler->fileName = '[name]';
            $compiler->compile([$scssFile => self::DEST_FILE], $theme->basePath, $template);
        }
        $this->registerInlineCssFile($cssFile);
    }

    /**
     * Compile a scss file called in a template, identified by a hash
     * then registers the css on the view
     * 
     * @param  string         $path
     * @param  string         $hash
     * @param  ThemeInterface $theme
     * @param  string         $template
     * @param  array          $options
     * @param  bool           $force
     */
    public function compileInlineFile(string $path, string $hash, ThemeInterface $theme, string $template, array $options = [], bool $force = false)
    {
        $cssFile = $this->getDestFile($hash);
        $cssFileExists = file_exists($cssFile);
        $scssFile = realpath(dirname($template) . DIRECTORY_SEPARATOR . $path);
        if (!$scssFile) {
            throw InlineScssException::noFile($path, $template);
        }
        if ($force or !$cssFileExists or filemtime($scssFile) > filemtime($cssFile)) {
            $compiler = $theme->getScssCompiler($options);
            $compiler->publicFolder = dirname($cssFile);
            $compiler->fileName = '[name]';
            $scssFile = StringHelper::replaceBeginning($scssFile, $theme->basePath . DIRECTORY_SEPARATOR, '');
            $compiler->compile([$scssFile => self::DEST_FILE], $theme->basePath);
        }
        $this->registerInlineCssFile($cssFile);
    }

    /**
     * Clear all scss and css files
     */
    public function clearCaches()
    {
        FileHelper::removeDirectory(\Craft::getAlias(self::CSS_SRC_FOLDER));
        FileHelper::removeDirectory(\Craft::getAlias(self::CSS_DEST_FOLDER));
    }

    /**
     * Register a css file on the view
     * 
     * @param string $cssFile
     */
    protected function registerInlineCssFile(string $cssFile)
    {
        $paths = \Craft::$app->assetManager->publish(dirname($cssFile));
        \Craft::$app->view->registerCssFile($paths[1] . '/' . basename($cssFile));
    }

    /**
     * Get a source file path identified by a hash
     * 
     * @param  string $hash
     * @return string
     */
    protected function getSrcFile(string $hash): string
    {
        return \Craft::getAlias(self::CSS_SRC_FOLDER . '/' . $hash . '.scss');
    }

    /**
     * Get a destination file path identified by a hash
     * 
     * @param  string $hash
     * @return string
     */
    protected function getDestFile(string $hash)
    {
        return \Craft::getAlias(self::CSS_DEST_FOLDER . '/' . $hash . '/' . self::DEST_FILE);
    }
}