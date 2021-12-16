<?php
namespace Ryssbowh\CraftThemes\stubs;

class BlockProviderStub extends Stub
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
            '$NAMESPACE' => $this->namespace . '\\blockProviders',
            '$HANDLE' => $this->handle,
            '$NAME' => $this->name,
            '$CLASSNAME' => $this->className
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getStub(): string
    {
        return 'blockProvider.php';
    }

    /**
     * @inheritDoc
     */
    protected function getDestination(): string
    {
        return $this->basePath . '/blockProviders/' . $this->className . '.php';
    }
}