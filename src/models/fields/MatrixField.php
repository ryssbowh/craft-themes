<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\ViewMode;

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
     * @return ViewMode
     */
    public function getViewMode(): ViewMode
    {
        return $this->matrix->display->viewMode;
    }
}