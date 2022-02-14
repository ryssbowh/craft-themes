<?php
namespace Ryssbowh\CraftThemes\stubs;

class ComposerFileStub extends Stub
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
     * @inheritDoc
     */
    protected function getVariables(): array
    {
        return [
            '$NAMESPACE' => str_replace('\\', '\\\\', $this->namespace),
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
        return 'composer.json';
    }

    /**
     * @inheritDoc
     */
    protected function getDestination(): string
    {
        return $this->basePath . '/composer.json';
    }
}