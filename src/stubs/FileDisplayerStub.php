<?php
namespace Ryssbowh\CraftThemes\stubs;

class FileDisplayerStub extends Stub
{
    /**
     * @var string
     */
    public $namespace;

    /**
     * @var string
     */
    public $handle;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $className;

    /**
     * @var string
     */
    public $themeHandle;

    /**
     * @inheritDoc
     */
    protected function getVariables(): array
    {
        return [
            '$THEMEHANDLE' => $this->themeHandle,
            '$NAMESPACE' => $this->namespace . '\\models\\fileDisplayers',
            '$HANDLE' => $this->handle,
            '$NAME' => $this->name,
            '$CLASSNAME' => $this->className,
            '$OPTIONSNAMESPACE' => $this->namespace . '\\models\\fileDisplayerOptions',
            '$OPTIONSCLASS' => $this->className . 'Options',
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getStub(): string
    {
        return 'fileDisplayer.php';
    }

    /**
     * @inheritDoc
     */
    protected function getDestination(): string
    {
        return $this->basePath . '/models/fileDisplayers/' . $this->className . '.php';
    }
}