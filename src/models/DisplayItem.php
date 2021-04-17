<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\RenderableInterface;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use craft\base\Model;
use craft\helpers\StringHelper;

abstract class DisplayItem extends Model implements RenderableInterface
{
    public $id;
    public $display_id;
    public $labelHidden = false;
    public $labelVisuallyHidden = false;
    public $hidden = false;
    public $visuallyHidden = false;
    public $dateCreated;
    public $dateUpdated;
    public $uid;
    
    /**
     * Order when used in matrix or group
     * @var int
     */
    public $order;

    protected $_display;

    public function defineRules(): array
    {
        return [
            ['display_id', 'integer'],
            [['labelHidden', 'hidden', 'visuallyHidden', 'labelVisuallyHidden'], 'boolean'],
            [['dateCreated', 'dateUpdated', 'uid', 'id'], 'safe']
        ];
    }

    public function getConfig(): array
    {
        return [
            'labelHidden' => $this->labelHidden,
            'labelVisuallyHidden' => $this->labelVisuallyHidden,
            'hidden' => $this->hidden,
            'visuallyHidden' => $this->visuallyHidden,
            'uid' => $this->uid ?? StringHelper::UUID()
        ];
    }

    public function getDisplay(): ?Display
    {
        if (!$this->display_id) {
            return null;
        }
        if (is_null($this->_display)) {
            $this->_display = Themes::$plugin->display->getById($this->display_id);
        }
        return $this->_display;
    }

    public function setDisplay(Display $display)
    {
        $this->_display = $display;
    }

    public function getViewMode(): ViewMode
    {
        return $this->display->viewMode;
    }

    public function getLayout(): Layout
    {
        return $this->viewMode->layout;
    }
}