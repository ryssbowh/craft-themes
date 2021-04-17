<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\BlockException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use Ryssbowh\CraftThemes\interfaces\RenderableInterface;
use Ryssbowh\CraftThemes\models\blockOptions\NoOptions;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use craft\base\Element;
use craft\base\Model;
use craft\helpers\StringHelper;

abstract class Block extends Model implements BlockInterface, RenderableInterface
{
    /**
     * @var string
     */
    public static $handle;

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $smallDescription = '';

    /**
     * @var boolean
     */
    public $hasOptions = false;

    /**
     * @var array
     */
    private $_options = [];

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $region;

    /**
     * @var int
     */
    public $layout_id;

    /**
     * @var string
     */
    public $provider;

    /**
     * @var int
     */
    public $order;

    /**
     * @var bool
     */
    public $active;

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var DateTime
     */
    public $dateCreated;

    /**
     * @var DateTime
     */
    public $dateUpdated;

    /**
     * @var string
     */
    public $uid;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        if (!$this::$handle) {
            throw BlockException::noHandle(get_called_class());
        }
        if (!$this->provider) {
            throw BlockException::noProvider(get_called_class());
        }
        if (!$this->name) {
            throw BlockException::noName(get_called_class());
        }
    }

    /**
     * Project config to be saved
     * 
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'region' => $this->region,
            'handle' => $this->handle,
            'provider' => $this->provider,
            'order' => $this->order,
            'active' => $this->active,
            'options' => $this->options,
            'uid' => $this->uid ?? StringHelper::UUID()
        ];
    }

    /**
     * @inheritDoc
     */
    public function getLayout(): ?Layout
    {
        if (!$this->layout_id) {
            return null;
        }
        return Themes::$plugin->layouts->getById($this->layout_id);
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return [
            [['region', 'handle', 'provider', 'order', 'active', 'layout_id'], 'required'],
            [['region', 'handle', 'provider'], 'string'],
            ['active', 'boolean'],
            [['order', 'layout_id'], 'number'],
            [['dateCreated', 'dateUpdated', 'uid', 'id', 'safe'], 'safe']
        ];
    }

    /**
     * @inheritDoc
     */
    public function provider(): BlockProviderInterface
    {
        return Themes::$plugin->blockProviders->getByHandle($this->provider);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return $this::$handle;
    }

    /**
     * @inheritDoc
     */
    public function getMachineName(): string
    {
        return $this->provider . '_' . $this::$handle;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsHtml(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getTemplateSuggestions(): array
    {
        return ['blocks/block-' . $this->getMachineName(), 'blocks/block'];
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['handle', 'hasOptions', 'optionsHtml']);
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): Model
    {
        return \Yii::configure($this->getOptionsModel(), $this->_options);
    }

    /**
     * Set options
     * 
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->_options = $options;
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): Model
    {
        return new NoOptions;
    }

    public function render(Element $element): string
    {
        return Themes::$plugin->view->renderBlock($this, $element);
    }

    public function __toString()
    {
        return $this->render();
    }
}