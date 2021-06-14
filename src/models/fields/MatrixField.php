<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\ViewMode;
use craft\base\Field as BaseField;

class MatrixField extends CraftField
{
    /**
     * @var CraftField
     */
    protected $_matrix;

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
     * Get view mode associated to this field
     * 
     * @return ViewMode
     */
    public function getViewMode(): ViewMode
    {
        return $this->matrix->display->viewMode;
    }
}