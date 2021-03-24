<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\models\ViewMode;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use craft\base\Model;

abstract class DisplayItem extends Model
{
    public $id;
    public $display_id;
    public $dateCreated;
    public $dateUpdated;
    public $uid;

    protected $_display;

    public function rules()
    {
        return [
            ['display_id', 'integer']
        ];
    }

    public function getDisplay(): Display
    {
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