<?php
namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;

/**
 * Handles a field inside a matrix block
 */
class MatrixField extends CraftField
{
    /**
     * @var Matrix
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
     * Matrix field getter
     * 
     * @return Matrix
     */
    public function getMatrix(): ?Matrix
    {
        if (is_null($this->_matrix)) {
            $this->_matrix = Themes::$plugin->matrix->getMatrixForField($this->id);
        }
        return $this->_matrix;
    }

    /**
     * Matrix setter
     * 
     * @param Matrix $matrix
     */
    public function setMatrix(Matrix $matrix)
    {
        $this->_matrix = $matrix;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->craftField->name;
    }

    /**
     * @inheritDoc
     */
    public function getDisplay(): DisplayInterface
    {
        return $this->matrix->display;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $config = parent::getConfig();
        unset($config['display_id']);
        return $config;
    }

    /**
     * The value is required here, it must come from a MatrixBlock
     * 
     * @param  mixed $value
     * @return string
     */
    public function render($value = null): string
    {
        if ($value === null) {
            return '';
        }
        return Themes::$plugin->view->renderField($this, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCanBeCached(): bool
    {
        return false;
    }
}