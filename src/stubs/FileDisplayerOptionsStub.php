<?php
namespace Ryssbowh\CraftThemes\stubs;

class FileDisplayerOptionsStub extends Stub
{
    /**
     * @var string
     */
    public $namespace;

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
            '$NAMESPACE' => $this->namespace . '\\models\\fileDisplayerOptions',
            '$CLASSNAME' => $this->className,
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getStub(): string
    {
        return 'fileDisplayerOptions.php';
    }

    /**
     * @inheritDoc
     */
    protected function getDestination(): string
    {
        return $this->basePath . '/models/fileDisplayerOptions/' . $this->className . '.php';
    }
}