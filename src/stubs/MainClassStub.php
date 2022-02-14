<?php
namespace Ryssbowh\CraftThemes\stubs;

class MainClassStub extends Stub
{
    /**
     * @var string
     */
    public $namespace;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $handle;

    /**
     * @inheritDoc
     */
    protected function getVariables(): array
    {
        return [
            '$NAMESPACE' => $this->namespace,
            '$MAINCLASS' => $this->name,
            '$HANDLE' => $this->handle,
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getStub(): string
    {
        return 'mainClass.php';
    }

    /**
     * @inheritDoc
     */
    protected function getDestination(): string
    {
        return $this->basePath . '/' . $this->name . '.php';
    }
}