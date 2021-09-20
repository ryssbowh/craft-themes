<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\interfaces\DisplayItemInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use craft\base\Model;
use craft\helpers\StringHelper;

abstract class DisplayItem extends Model implements DisplayItemInterface
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $display_id;

    /**
     * @var boolean
     */
    public $labelHidden = false;

    /**
     * @var boolean
     */
    public $labelVisuallyHidden = false;

    /**
     * @var boolean
     */
    public $hidden = false;

    /**
     * @var boolean
     */
    public $visuallyHidden = false;

    /**
     * @var string
     */
    public $uid;

    /**
     * @var DisplayInterface
     */
    protected $_display;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            ['display_id', 'integer'],
            [['labelHidden', 'hidden', 'visuallyHidden', 'labelVisuallyHidden'], 'boolean'],
            [['uid', 'id'], 'safe']
        ];
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return [
            'labelHidden' => $this->labelHidden,
            'labelVisuallyHidden' => $this->labelVisuallyHidden,
            'hidden' => $this->hidden,
            'visuallyHidden' => $this->visuallyHidden,
            'display_id' => $this->display->uid,
            'uid' => $this->uid ?? StringHelper::UUID()
        ];
    }

    /**
     * @inheritDoc
     */
    public function getDisplay(): DisplayInterface
    {
        if (is_null($this->_display)) {
            $this->_display = Themes::$plugin->displays->getById($this->display_id);
        }
        return $this->_display;
    }

    /**
     * @inheritDoc
     */
    public function setDisplay(DisplayInterface $display)
    {
        $this->_display = $display;
    }

    /**
     * @inheritDoc
     */
    public function getViewMode(): ViewModeInterface
    {
        return $this->display->viewMode;
    }

    /**
     * @inheritDoc
     */
    public function getLayout(): LayoutInterface
    {
        return $this->viewMode->layout;
    }
}