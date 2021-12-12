<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\services\DisplayService;
use Ryssbowh\CraftThemes\traits\HasDisplays;
use craft\base\Model;

/**
 * Class for view modes
 */
class ViewMode extends Model implements ViewModeInterface
{
    use HasDisplays;

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $layout_id;

    /**
     * @var name
     */
    public $name;

    /**
     * @var string
     */
    public $handle;

    /**
     * @var string
     */
    public $uid;

    /**
     * @var LayoutInterface
     */
    protected $_layout;

    /**
     * @inheritdoc
     */
    public function defineRules(): array
    {
        return [
            [['name', 'handle'], 'required'],
            [['name', 'handle'], 'string'],
            ['layout_id', 'integer'],
            [['uid', 'id', 'displays'], 'safe'],
            ['layout', function () {
                if (!$this->layout) {
                    $this->addError('layout', \Craft::t('themes', 'Layout is required'));
                }
            }],
        ];
    }

    /**
     * Has errors getter
     * 
     * @return bool
     */
    public function getHasErrors(): bool
    {
        return $this->hasErrors();
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return [
            'name' => $this->name,
            'handle' => $this->handle,
            'layout_id' => $this->layout->uid
        ];
    }

    /**
     * @inheritDoc
     */
    public function eagerLoad(string $prefix = '', int $level = 0): array
    {
        $with = [];
        foreach ($this->getVisibleDisplays() as $display) {
            $with = array_merge($with, $display->item->eagerLoad($prefix, $level));
        }
        return $with;
    }

    /**
     * @inheritDoc
     */
    public function addDisplay(DisplayInterface $display)
    {
        if ($display->type != DisplayService::TYPE_GROUP) {
            throw ViewModeException::notAGroup($this);
        }
        $display->viewMode = $this;
        $displays = $this->displays;
        $displays[] = $display;
        $this->displays = $displays;
    }

    /**
     * @inheritDoc
     */
    public function getAllDisplays(): array
    {
        return Themes::$plugin->displays->getForViewMode($this, false);
    }

    /**
     * @inheritDoc
     */
    public function setDisplays(?array $displays)
    {
        if (is_array($displays)) {
            foreach ($displays as $display) {
                $display->viewMode = $this;
            }
        }
        $this->_displays = $displays;
    }

    /**
     * @inheritDoc
     */
    public function getLayout(): LayoutInterface
    {
        if (is_null($this->_layout)) {
            $this->_layout = Themes::$plugin->layouts->getById($this->layout_id);
        }
        return $this->_layout;
    }

    /**
     * @inheritDoc
     */
    public function setLayout(LayoutInterface $layout)
    {
        $this->_layout = $layout;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['displays', 'hasErrors']);
    }

    /**
     * @inheritDoc
     */
    protected function loadDisplays(): array
    {
        return Themes::$plugin->displays->getForViewMode($this);
    }
}
