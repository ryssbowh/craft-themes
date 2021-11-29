<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\ViewModeException;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\services\DisplayService;
use craft\base\Model;

/**
 * Class for view modes
 */
class ViewMode extends Model implements ViewModeInterface
{
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
     * @var array
     */
    protected $_displays;

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
     * @inheritDoc
     */
    public function afterValidate()
    {
        foreach ($this->displays as $display) {
            $display->validate();
        }
        parent::afterValidate();
    }

    /**
     * @inheritDoc
     */
    public function hasErrors($attribute = null)
    {
        if ($attribute !== null) {
            return parent::hasErrors($attribute);
        }
        foreach ($this->displays as $display) {
            if ($display->hasErrors()) {
                return true;
            }
        }
        return parent::hasErrors();
    }

    /**
     * @inheritDoc
     */
    public function getErrors($attribute = null)
    {
        $errors = parent::getErrors();
        foreach ($this->displays as $index => $display) {
            if ($display->hasErrors()) {
                $errors['displays'][$index] = $display->getErrors();
            }
        }
        if ($attribute === 'displays') {
            return $errors['displays'] ?? [];
        }
        if ($attribute !== null) {
            return parent::getErrors($attribute);
        }
        return $errors;
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
    public function getDisplays(): array
    {
        if (is_null($this->_displays)) {
            $this->_displays = Themes::$plugin->displays->getForViewMode($this);
        }
        return $this->_displays;
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
    public function getVisibleDisplays(): array
    {
        return array_filter($this->displays, function ($display) {
            return $display->group_id === null and $display->item->isVisible();
        });
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
}
