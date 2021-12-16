<?php
namespace Ryssbowh\CraftThemes\stubs;

class RegionsStub extends Stub
{
    /**
     * @inheritDoc
     */
    protected function getVariables(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    protected function getStub(): string
    {
        return 'regions.twig';
    }

    /**
     * @inheritDoc
     */
    protected function getDestination(): string
    {
        return $this->basePath . '/templates/regions.twig';
    }
}