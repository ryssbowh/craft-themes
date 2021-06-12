<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\BlockException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\BlockOptions;
use craft\base\Element;
use craft\base\Model;
use craft\helpers\StringHelper;

abstract class Block extends Model implements BlockInterface
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
     * @var array
     */
    protected $_options = [];

    /**
     * @var array
     */
    protected $_optionsModel;

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
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return [
            'region' => $this->region,
            'handle' => $this->handle,
            'provider' => $this->provider,
            'order' => $this->order,
            'active' => $this->active,
            'options' => $this->options->getConfig(),
            'uid' => $this->uid ?? StringHelper::UUID()
        ];
    }

    /**
     * @inheritDoc
     */
    public function getLayout(): ?LayoutInterface
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
            [['region', 'handle', 'provider', 'order', 'active'], 'required'],
            [['region', 'handle', 'provider'], 'string'],
            ['active', 'boolean'],
            [['order', 'layout_id'], 'number'],
            [['dateCreated', 'dateUpdated', 'uid', 'id', 'safe'], 'safe'],
            ['options', function () {
                $options = $this->options;
                if (!$options->validate()) {
                    $this->addError('options', $options->getErrors());
                }
            }]
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
    public function fields()
    {
        return array_merge(parent::fields(), ['handle', 'options', 'errors']);
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): BlockOptions
    {
        if ($this->_optionsModel === null) {
            $this->_optionsModel = \Yii::configure($this->getOptionsModel(), $this->_options);
        }
        return $this->_optionsModel;
    }

    /**
     * @inheritDoc
     */
    public function setOptions($options)
    {
        if (is_string($options)) {
            $options = json_decode($options, true);
        }
        $this->getOptions()->setAttributes($options);
    }

    /**
     * @inheritDoc
     */
    public function getOptionsModel(): BlockOptions
    {
        return new BlockOptions;
    }

    /**
     * @inheritDoc
     */
    public function afterSave()
    {
        $this->options->afterSave($this);
    }

    /**
     * @inheritDoc
     */
    public function render(Element $element): string
    {
        return Themes::$plugin->view->renderBlock($this, $element);
    }
}