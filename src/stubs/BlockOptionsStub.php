<?php
namespace Ryssbowh\CraftThemes\stubs;

class BlockOptionsStub extends Stub
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
            '$NAMESPACE' => $this->namespace . '\\models\\blockOptions',
            '$CLASSNAME' => $this->className,
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getStub(): string
    {
        return 'blockOptions.php';
    }

    /**
     * @inheritDoc
     */
    protected function getDestination(): string
    {
        return $this->basePath . '/models/blockOptions/' . $this->className . '.php';
    }
}