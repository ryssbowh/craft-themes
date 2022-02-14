<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;

/**
 * Base class for all field displayer options
 */
class FieldDisplayerOptions extends EventDefinableOptions
{
    /**
     * @var FieldDisplayerInterface
     */
    protected $_displayer;

    /**
     * Field displayer getter
     * 
     * @return FieldDisplayerInterface
     */
    public function getDisplayer(): FieldDisplayerInterface
    {
        return $this->_displayer;
    }

    /**
     * Field displayer stter
     * 
     * @param FieldDisplayerInterface $displayer
     */
    public function setDisplayer(FieldDisplayerInterface $displayer)
    {
        $this->_displayer = $displayer;
    }

    /**
     * @inheritDoc
     */
    protected function reservedWords(): array
    {
        return array_merge(parent::reservedWords(), ['displayer']);
    }
}