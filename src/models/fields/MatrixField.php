<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;

class MatrixField extends CraftField
{
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'matrix_field';
    }

    /**
     * @inheritDoc
     */
    public function getMatrix(): ?CraftField
    {
        return Themes::$plugin->matrix->getMatrixForField($this->id);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->craftField->name;
    }

    /**
     * Get view mode associated to this field
     * 
     * @return ViewModeInterface
     */
    public function getViewMode(): ViewModeInterface
    {
        return $this->matrix->display->viewMode;
    }
}