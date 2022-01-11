<?php
namespace Ryssbowh\CraftThemes\scss;

use Ryssbowh\CraftThemes\exceptions\ScssCompilerException;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use ScssPhp\ScssPhp\Compiler as ScssCompiler;
use ScssPhp\ScssPhp\OutputStyle;
use craft\helpers\FileHelper;
use craft\helpers\StringHelper;
use yii\base\BaseObject;

/**
 * Scss compiling helpers
 */
class Compiler extends BaseObject
{
    public $theme;

    public $sourcemaps = 'none';

    public $style = OutputStyle::EXPANDED;

    public $cleanDest = true;

    public $nodePath = 'node_modules';

    public $fileName = '[name].[hash]';

    public $hashExtractedAssets = true;

    public $extractedAssetsFolder = 'extracted';

    public $manifest = true;

    public $manifestName = 'manifest';

    public $hashMethod = 'crc32b';

    protected $manifests = [];

    protected $compiler;

    protected $relativeSrcFile;

    protected $srcFile;

    protected $relativeSrcPath;

    protected $srcPath;

    protected $relativeDestPath;

    protected $destPath;

    protected $destFile;

    protected $currentTheme;

    protected $sourcemapsMap = [
        'none' => ScssCompiler::SOURCE_MAP_NONE,
        'inline' => ScssCompiler::SOURCE_MAP_INLINE,
        'file' => ScssCompiler::SOURCE_MAP_FILE,
    ];

    public function __construct(ThemeInterface $theme, string $relativeSrcFile, string $relativeDestFile, $config = [])
    {
        $this->theme = $theme;
        $this->currentTheme = $theme;
        $this->srcFile = $this->theme->basePath . DIRECTORY_SEPARATOR . trim($relativeSrcFile, DIRECTORY_SEPARATOR);
        if (!file_exists($this->srcFile)) {
            throw ScssCompilerException::fileNotFound($this->srcFile);
        }
        if (strpos($relativeDestFile, '.css') === false) {
            $pathinfo = pathinfo($this->srcFile);
            $relativeDestFile  = trim($relativeDestFile, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $pathinfo['filename'] . '.css';
        }
        $this->destFile = \Yii::getAlias('@webroot/' . $relativeDestFile);
        $this->destPath = dirname($this->destFile);
        $this->relativeDestPath = dirname(trim($relativeDestFile, DIRECTORY_SEPARATOR));
        $this->relativeSrcFile = trim($relativeSrcFile, DIRECTORY_SEPARATOR);
        $this->relativeSrcPath = dirname($this->relativeSrcFile);
        $this->srcFile = $this->theme->basePath . DIRECTORY_SEPARATOR . $relativeSrcFile;
        $this->srcPath = dirname($this->srcFile);
        parent::__construct($config);
        $this->compiler = $this->getCompiler();
    }

    public function init()
    {
        parent::init();
        if (!isset($this->sourcemapsMap[$this->sourcemaps])) {
            throw ScssCompilerException::sourcemapInvalid(array_keys($this->sourcemapsMap));
        }
    }

    public function run()
    {
        if ($this->cleanDest) {
            FileHelper::removeDirectory($this->destPath);
        }
        if (!file_exists($this->destPath)) {
            FileHelper::createDirectory($this->destPath);
        }
        $pathinfo = pathinfo($this->destFile);
        $results = $this->compiler->compileString(file_get_contents($this->srcFile));
        $css = $results->getCss();
        $fileName = str_replace('[hash]', $this->getHash($css), $this->fileName);
        $fileName = str_replace('[name]', $pathinfo['filename'], $fileName . '.css');
        $this->destFile = $this->destPath . DIRECTORY_SEPARATOR . $fileName;
        file_put_contents($this->destFile, $css);
        if ($this->sourcemaps == 'file') {
            file_put_contents($this->destPath . DIRECTORY_SEPARATOR . $pathinfo['filename'] . '.css.map', $results->getSourceMap());
        }
        $this->addToManifest($pathinfo['filename'] . '.css', $fileName);
        $this->writeManifest();
    }

    protected function getCompiler(): ScssCompiler
    {
        $_this = $this;
        $compiler = new ScssCompiler();
        $compiler->setOutputStyle($this->style);
        $compiler->setSourceMap($this->sourcemapsMap[$this->sourcemaps]);
        $pathinfo = pathinfo($this->destFile);
        $sourcemapsFile = $pathinfo['filename'] . '.css.map';
        $compiler->setSourceMapOptions([
            'sourceMapURL' => DIRECTORY_SEPARATOR . $this->relativeDestPath . DIRECTORY_SEPARATOR . $sourcemapsFile,
        ]);
        $compiler->addImportPath(function ($path) use ($_this) {
            return $_this->import($path);
        });
        $compiler->registerFunction('url', function ($args, $file) use ($_this) {
            return $_this->url($args, $file);
        }, ['url']);
        return $compiler;
    }

    protected function import(string $path)
    {
        if (!StringHelper::endsWith($path, '.scss')) {
            $path .= '.scss';
        }
        if (substr($path, 0, 1) == '~') {
            return $this->importNodeFile($path);
        }
        return $this->importLocalFile($path);
    }

    protected function importNodeFile(string $path, ?ThemeInterface $theme = null)
    {
        if (is_null($theme)) {
            $theme = $this->theme;
        }
        $nodePath = $theme->basePath . DIRECTORY_SEPARATOR . $this->nodePath;
        $path2 = $nodePath . DIRECTORY_SEPARATOR . substr($path, 1);
        if (file_exists($path2)) {
            return $path2;
        }
        if ($parent = $theme->parent) {
            return $this->importNodeFile($path, $parent);
        }
        return null;
    }

    protected function importLocalFile(string $path, ?ThemeInterface $theme = null)
    {
        if (is_null($theme)) {
            $theme = $this->theme;
        }
        $currentDir = $this->getCurrentDir();
        $fullPath = $theme->basePath . DIRECTORY_SEPARATOR . $this->relativeSrcPath . DIRECTORY_SEPARATOR . $path;
        if (file_exists($fullPath)) {
            return $fullPath;
        }
        if ($parent = $theme->parent) {
            return $this->importLocalFile($path, $parent);
        }
        return null;
    }

    protected function url($args, $arg): string
    {
        $this->compiler->assertString($args[0], 'url');
        $path = $this->compiler->compileValue($args[0]);
        $path = urldecode($path);
        if (preg_match('/^"(.*)"$/', $path, $matches)) {
            $path = $matches[1] ?? $path;
        }
        if (preg_match('/^\'(.*)\'$/', $path, $matches)) {
            $path = $matches[1] ?? $path;
        }
        if (StringHelper::startsWithAny($path, ['http://', 'https://', 'data:image', '#'])) {
            return 'url("' . $path . '")';
        }
        $filePath = $this->getAssetPath($path);
        if ($filePath) {
            $url = $this->extractFile($filePath);
            if ($url) {
                return 'url("' . $url . '")';
            }
        }
        return 'url("' . $path . '")';
    }

    protected function extractFile(string $path, ?ThemeInterface $theme = null): ?string
    {
        if ($theme === null) {
            $theme = $this->theme;
        }
        if (!StringHelper::startsWith($path, $theme->basePath)) {
            if ($parent = $theme->parent) {
                return $this->extractFile($path, $parent);
            }
            return null;
        }
        list($path, $suffix) = $this->normalizeSuffix($path);
        $relativePath = $this->getExtractedDestPath($theme, $path);
        $dest = $this->destPath . DIRECTORY_SEPARATOR . $relativePath;
        if (!file_exists(dirname($dest))) {
            mkdir(dirname($dest), 0777, true);
        }
        if (!file_exists($dest)) {
            copy($path, $dest);
        }
        $this->addToManifest($relativePath . $suffix);
        return DIRECTORY_SEPARATOR . $this->relativeDestPath . DIRECTORY_SEPARATOR . $relativePath . $suffix;
    }

    protected function getExtractedDestPath(ThemeInterface $theme, string $path)
    {
        if (!$this->hashExtractedAssets) {
            return str_replace($theme->basePath . DIRECTORY_SEPARATOR, '', $path);
        }
        $hashed = $this->getHash($path);
        $fileInfo = pathinfo($path);
        if (!isset($fileInfo['extension'])) {
            dd($path, $fileInfo);
        }
        return $this->extractedAssetsFolder . DIRECTORY_SEPARATOR . $hashed . '.' . $fileInfo['extension'];
    }

    protected function getAssetPath(string $path, ?ThemeInterface $theme = null): ?string
    {
        if (is_null($theme)) {
            $theme = $this->theme;
        }
        list($path, $suffix) = $this->normalizeSuffix($path);
        $fullPath = realpath($this->getCurrentDir() . DIRECTORY_SEPARATOR . $path);
        if ($fullPath and file_exists($fullPath)) {
            return $fullPath . $suffix;
        }
        if ($parent = $theme->parent) {
            return $this->getAssetPath($path, $parent);
        }
        return null;
    }

    protected function getCurrentFile(): string
    {
        $currentFile = $this->compiler->getSourcePosition()[0];
        if (!$currentFile) {
            $currentFile = $this->srcFile;
        }
        return $currentFile;
    }

    protected function normalizeSuffix(string $path)
    {
        $suffix = '';
        $suffixIndex = strpos($path, '?');
        if ($suffixIndex === false) {
            $suffixIndex = strpos($path, '#');
        }
        if ($suffixIndex !== false) {
            $suffix = substr($path, $suffixIndex);
            $path = substr($path, 0, $suffixIndex);
        }
        return [$path, $suffix];
    }

    protected function getCurrentDir(): string
    {
        return dirname($this->getCurrentFile());
    }

    protected function addToManifest(string $srcPath, ?string $destPath = null)
    {
        if ($destPath === null) {
            $destPath = $srcPath;
        }
        list($destPath, $suffix) = $this->normalizeSuffix($destPath);
        $this->manifests[$srcPath] = $destPath;
    }

    protected function getHash(string $data): string
    {
        return hash($this->hashMethod, $data);
    }

    protected function writeManifest()
    {
        if (!$this->manifest) {
            return;
        }
        $dest = $this->destPath . DIRECTORY_SEPARATOR . $this->manifestName . '.json';
        file_put_contents($dest, json_encode($this->manifests, JSON_PRETTY_PRINT));
    }
}