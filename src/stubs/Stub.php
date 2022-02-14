<?php
namespace Ryssbowh\CraftThemes\stubs;

use Ryssbowh\CraftThemes\exceptions\CreatorException;
use craft\helpers\FileHelper;
use yii\base\BaseObject;

abstract class Stub extends BaseObject
{
    /**
     * @var string
     */
    public $basePath;

    /**
     * Write stub on disk
     */
    public function write()
    {
        $content = $this->getRawContent();
        $variables = $this->getVariables();
        $content = str_replace(array_keys($variables), array_values($variables), $content);
        $this->ensureFileDoesntExists();
        $this->ensureFolderExists();
        file_put_contents($this->getDestination(), $content);
    }

    /**
     * Create the direcory for this stub if not there
     */
    protected function ensureFolderExists()
    {
        $folder = dirname($this->getDestination());
        if (!file_exists($folder)) {
            FileHelper::createDirectory($folder);
        }
    }

    /**
     * Check if the file already exists
     *
     * @throws CreatorException
     */
    protected function ensureFileDoesntExists()
    {
        if (file_exists($this->getDestination())) {
            throw CreatorException::fileExists($this->getDestination());
        }
    }

    /**
     * Get the source stub file path
     * 
     * @return string
     */
    protected function getSourceStub(): string
    {
        return __DIR__ . '/stubs/' . $this->stub;
    }

    /**
     * Get the content of the source stub
     *
     * @return string
     */
    public function getRawContent(): string
    {
        return file_get_contents($this->getSourceStub());
    }

    /**
     * Get the destination file path
     * 
     * @return string
     */
    abstract protected function getDestination(): string;

    /**
     * Get variables to replace in the source stub
     * 
     * @return array
     */
    abstract protected function getVariables(): array;

    /**
     * Get the source stub file name
     * 
     * @return string
     */
    abstract protected function getStub(): string;
}