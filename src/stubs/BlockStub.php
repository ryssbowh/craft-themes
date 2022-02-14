<?php
namespace Ryssbowh\CraftThemes\stubs;

class BlockStub extends Stub
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
    public $description;

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
            '$NAMESPACE' => $this->namespace . '\\models\\blocks',
            '$HANDLE' => $this->handle,
            '$NAME' => $this->name,
            '$CLASSNAME' => $this->className,
            '$DESCRIPTION' => $this->description,
            '$OPTIONSNAMESPACE' => $this->namespace . '\\models\\blockOptions',
            '$OPTIONSCLASS' => $this->className . 'Options',
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getStub(): string
    {
        return 'block.php';
    }

    /**
     * @inheritDoc
     */
    protected function getDestination(): string
    {
        return $this->basePath . '/models/blocks/' . $this->className . '.php';
    }
}