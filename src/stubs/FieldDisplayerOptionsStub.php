<?php
namespace Ryssbowh\CraftThemes\stubs;

class FieldDisplayerOptionsStub extends Stub
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
            '$NAMESPACE' => $this->namespace . '\\models\\fieldDisplayerOptions',
            '$CLASSNAME' => $this->className,
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getStub(): string
    {
        return 'fieldDisplayerOptions.php';
    }

    /**
     * @inheritDoc
     */
    protected function getDestination(): string
    {
        return $this->basePath . '/models/fieldDisplayerOptions/' . $this->className . '.php';
    }
}