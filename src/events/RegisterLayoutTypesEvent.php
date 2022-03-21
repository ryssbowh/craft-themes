<?php
namespace Ryssbowh\CraftThemes\events;

use Ryssbowh\CraftThemes\exceptions\LayoutException;
use Ryssbowh\CraftThemes\models\layouts\CategoryLayout;
use Ryssbowh\CraftThemes\models\layouts\CustomLayout;
use Ryssbowh\CraftThemes\models\layouts\EntryLayout;
use Ryssbowh\CraftThemes\models\layouts\GlobalLayout;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\models\layouts\TagLayout;
use Ryssbowh\CraftThemes\models\layouts\UserLayout;
use Ryssbowh\CraftThemes\models\layouts\VolumeLayout;
use yii\base\Event;

/**
 * Event to register layout types
 *
 * @since 3.1.0
 */
class RegisterLayoutTypesEvent extends Event
{
    /**
     * List of registered types
     * @var array
     */
    protected $_types = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->registerMany([
            'category' => CategoryLayout::class,
            'custom' => CustomLayout::class,
            'default' => Layout::class,
            'entry' => EntryLayout::class,
            'global' => GlobalLayout::class,
            'tag' => TagLayout::class,
            'user' => UserLayout::class,
            'volume' => VolumeLayout::class
        ]);
    }

    /**
     * types getter
     * 
     * @return array
     */
    public function getTypes(): array
    {
        return $this->_types;
    }

    /**
     * Register a type class
     *
     * @param string $name
     * @param string $class
     * @throws LayoutException
     */
    public function register(string $name, string $class)
    {
        if (isset($this->_types[$name])) {
            throw LayoutException::typeAlreadyDefined($name, $this->_types[$name]);
        }
        $this->_types[$name] = $class;
    }

    /**
     * Register many types
     * 
     * @param  array $types
     */
    public function registerMany(array $types)
    {
        foreach ($types as $name => $class) {
            $this->register($name, $class);
        }
    }
}