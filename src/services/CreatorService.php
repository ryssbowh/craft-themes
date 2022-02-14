<?php
namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\exceptions\CreatorException;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\stubs\BlockOptionsStub;
use Ryssbowh\CraftThemes\stubs\BlockProviderStub;
use Ryssbowh\CraftThemes\stubs\BlockStub;
use Ryssbowh\CraftThemes\stubs\ComposerFileStub;
use Ryssbowh\CraftThemes\stubs\FieldDisplayerOptionsStub;
use Ryssbowh\CraftThemes\stubs\FieldDisplayerStub;
use Ryssbowh\CraftThemes\stubs\FileDisplayerOptionsStub;
use Ryssbowh\CraftThemes\stubs\FileDisplayerStub;
use Ryssbowh\CraftThemes\stubs\MainClassStub;
use Ryssbowh\CraftThemes\stubs\RegionsStub;
use craft\helpers\FileHelper;

class CreatorService extends Service
{
    /**
     * Create a new theme
     * 
     * @param  string $name
     * @param  string $handle
     * @param  string $namespace
     * @param  string $folder
     * @param  string $className
     * @throws CreatorException
     * @return array
     */
    public function createTheme(string $name, string $handle = '' , string $namespace = '', string $className = '', string $folder = 'themes'): array
    {
        $folder = trim($folder, '/');
        $className = $this->sanitizeClassName($className, $name);
        if ($handle === '') {
            $handle = strtolower($className);
        }
        if ($namespace === '') {
            $namespace = 'themes\\' . $className;
        } else {
            $namespace = trim($namespace, '\\');
        }
        $basePath = \Craft::getAlias('@root/' . $folder . '/' . $handle);
        $this->validateHandle($handle);
        if (\Craft::$app->plugins->getPlugin($handle)) {
            throw CreatorException::pluginDefined($handle);
        }
        if (file_exists($basePath)) {
            throw CreatorException::folderExists($basePath);
        }
        FileHelper::createDirectory($basePath . '/src/templates/blocks');
        FileHelper::createDirectory($basePath . '/src/templates/fields');
        FileHelper::createDirectory($basePath . '/src/templates/files');
        FileHelper::createDirectory($basePath . '/src/templates/layouts');
        FileHelper::createDirectory($basePath . '/src/templates/regions');
        FileHelper::createDirectory($basePath . '/src/templates/groups');
        (new ComposerFileStub([
            'basePath' => $basePath,
            'namespace' => $namespace . '\\',
            'name' => $name,
            'handle' => $handle,
            'className' => $className
        ]))->write();
        (new MainClassStub([
            'basePath' => $basePath . '/src',
            'namespace' => $namespace,
            'name' => $className,
            'handle' => $handle,
        ]))->write();
        (new RegionsStub([
            'basePath' => $basePath . '/src',
        ]))->write();
        (new BlockStub([
            'basePath' => $basePath . '/src',
            'name' => $name . ' block',
            'handle' => $handle,
            'themeHandle' => $handle,
            'className' => $className . 'Block',
            'namespace' => $namespace,
            'description' => 'This is a block that does nothing yet'
        ]))->write();
        (new BlockOptionsStub([
            'basePath' => $basePath . '/src',
            'className' => 'TestBlockOptions',
            'namespace' => $namespace
        ]))->write();
        (new FieldDisplayerStub([
            'basePath' => $basePath . '/src',
            'name' => $name . ' field displayer',
            'handle' => $handle,
            'themeHandle' => $handle,
            'className' => $className . 'FieldDisplayer',
            'namespace' => $namespace
        ]))->write();
        (new FieldDisplayerOptionsStub([
            'basePath' => $basePath . '/src',
            'className' => $className . 'FieldDisplayerOptions',
            'namespace' => $namespace
        ]))->write();
        (new FileDisplayerStub([
            'basePath' => $basePath . '/src',
            'name' => $name . ' file displayer',
            'handle' => $handle,
            'themeHandle' => $handle,
            'className' => $className . 'FileDisplayer',
            'namespace' => $namespace
        ]))->write();
        (new FileDisplayerOptionsStub([
            'basePath' => $basePath . '/src',
            'className' => $className . 'FileDisplayerOptions',
            'namespace' => $namespace
        ]))->write();
        (new BlockProviderStub([
            'basePath' => $basePath . '/src',
            'name' => $name . ' block provider',
            'handle' => $handle,
            'themeHandle' => $handle,
            'className' => $className . 'BlockProvider',
            'namespace' => $namespace
        ]))->write();
        return [$handle, $className, $namespace];
    }

    /**
     * Create a new block class for a theme
     * 
     * @param  string $theme
     * @param  string $name
     * @param  string $handle
     * @param  string $description
     * @param  string $className
     * @return array
     */
    public function createBlock(string $theme, string $name, string $handle = '', string $description = '', string $className = ''): array
    {
        $theme = $this->getTheme($theme);
        $className = $this->sanitizeClassName($className, $name);
        if ($handle === '') {
            $handle = strtolower($className);
        }
        $this->validateHandle($handle);
        $basePath = $theme->basePath;
        $namespace = $this->getNamespace($theme);
        (new BlockStub([
            'basePath' => $theme->basePath,
            'name' => $name,
            'handle' => $handle,
            'themeHandle' => $theme->handle,
            'className' => $className,
            'namespace' => $namespace,
            'description' => $description
        ]))->write();
        (new BlockOptionsStub([
            'basePath' => $theme->basePath,
            'className' => $className . 'Options',
            'namespace' => $namespace
        ]))->write();
        return [$handle, $className, $namespace];
    }

    /**
     * Create a new block displayer for a theme
     * 
     * @param  string $theme
     * @param  string $name
     * @param  string $handle
     * @param  string $className
     * @return array
     */
    public function createBlockProvider(string $theme, string $name, string $handle = '', string $className = ''): array
    {
        $theme = $this->getTheme($theme);
        $className = $this->sanitizeClassName($className, $name);
        if ($handle === '') {
            $handle = strtolower($className);
        }
        $this->validateHandle($handle);
        $basePath = $theme->basePath;
        $namespace = $this->getNamespace($theme);
        (new BlockProviderStub([
            'basePath' => $theme->basePath,
            'name' => $name,
            'handle' => $handle,
            'themeHandle' => $theme->handle,
            'className' => $className,
            'namespace' => $namespace
        ]))->write();
        return [$handle, $className, $namespace];
    }

    /**
     * Create a new field displayer class for a theme
     * 
     * @param  string $theme
     * @param  string $name
     * @param  string $handle
     * @param  string $className
     * @return array
     */
    public function createFieldDisplayer(string $theme, string $name, string $handle = '', string $className = ''): array
    {
        $theme = $this->getTheme($theme);
        $className = $this->sanitizeClassName($className, $name);
        if ($handle === '') {
            $handle = strtolower($className);
        }
        $this->validateHandle($handle);
        $basePath = $theme->basePath;
        $namespace = $this->getNamespace($theme);
        (new FieldDisplayerStub([
            'basePath' => $theme->basePath,
            'name' => $name,
            'handle' => $handle,
            'themeHandle' => $theme->handle,
            'className' => $className,
            'namespace' => $namespace
        ]))->write();
        (new FieldDisplayerOptionsStub([
            'basePath' => $theme->basePath,
            'className' => $className . 'Options',
            'namespace' => $namespace
        ]))->write();
        return [$handle, $className, $namespace];
    }

    /**
     * Create a new file displayer class for a theme
     * 
     * @param  string $theme
     * @param  string $name
     * @param  string $handle
     * @param  string $className
     * @return array
     */
    public function createFileDisplayer(string $theme, string $name, string $handle = '', string $className = ''): array
    {
        $theme = $this->getTheme($theme);
        $className = $this->sanitizeClassName($className, $name);
        if ($handle === '') {
            $handle = strtolower($className);
        }
        $this->validateHandle($handle);
        $basePath = $theme->basePath;
        $namespace = $this->getNamespace($theme);
        (new FileDisplayerStub([
            'basePath' => $theme->basePath,
            'name' => $name,
            'handle' => $handle,
            'themeHandle' => $theme->handle,
            'className' => $className,
            'namespace' => $namespace
        ]))->write();
        (new FileDisplayerOptionsStub([
            'basePath' => $theme->basePath,
            'className' => $className . 'Options',
            'namespace' => $namespace
        ]))->write();
        return [$handle, $className, $namespace];
    }

    /**
     * Ensure a theme exists
     * 
     * @param  string $handle
     * @throws CreatorException
     * @return ThemeInterface
     */
    protected function getTheme(string $handle): ThemeInterface
    {
        if (!$theme = \Craft::$app->plugins->getPlugin($handle)) {
            throw CreatorException::themeUndefined($handle);
        }
        if (!$theme instanceof ThemeInterface) {
            throw CreatorException::themeUndefined($handle);
        }
        return $theme;
    }

    /**
     * Get the namespace of a theme
     * 
     * @param  ThemeInterface $theme
     * @return string
     */
    protected function getNamespace(ThemeInterface $theme): string
    {
        $elems = explode('\\', get_class($theme));
        array_pop($elems);
        return implode('\\', $elems);
    }

    /**
     * Validate a handle
     * 
     * @param  string $handle
     * @throws CreatorException
     */
    protected function validateHandle(string $handle)
    {
        if (!preg_match('/^[a-zA-Z]+$/', $handle)) {
            throw CreatorException::handleInvalid($handle);
        }
    }

    /**
     * Make up a class name from a name and validates it
     * 
     * @param  string $className
     * @param  string $name
     * @return string
     * @throws CreatorException
     */
    protected function sanitizeClassName(string $className, string $name): string
    {
        if ($className === '') {
            $className = preg_replace('/([^A-Za-z]+)/', '', $name);
        }
        if (!preg_match('/^[a-zA-Z]+$/', $className)) {
            throw CreatorException::classnameInvalid($className);
        }
        return $className;
    }
}