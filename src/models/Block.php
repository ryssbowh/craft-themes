<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\BlockException;
use Ryssbowh\CraftThemes\interfaces\BlockCacheStrategyInterface;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\BlockCacheStrategyOptions;
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
    public $order = 0;

    /**
     * @var bool
     */
    public $active = true;

    /**
     * @var string
     */
    public $uid;

    /**
     * @var array
     */
    protected $_optionsModel;

    /**
     * @var LayoutInterface
     */
    protected $_layout;

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
        $options = $this->options->getConfig();
        $strategyOptions = $this->getCacheStrategyOptions();
        if ($strategyOptions) {
            $options = array_merge($options, $strategyOptions->getConfig());
        }
        return [
            'layout_id' => $this->layout->uid,
            'region' => $this->region,
            'handle' => $this->handle,
            'provider' => $this->provider,
            'order' => $this->order,
            'active' => $this->active,
            'options' => $options
        ];
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
    public function defineRules(): array
    {
        return [
            [['region', 'handle', 'provider', 'order', 'active'], 'required'],
            [['region', 'handle', 'provider'], 'string'],
            ['active', 'boolean'],
            [['order', 'layout_id'], 'number'],
            [['uid', 'id', 'safe'], 'safe'],
            ['layout', function () {
                if (!$this->layout) {
                    $this->addError('layout', \Craft::t('themes', 'Layout is required'));
                }
            }],
            ['region', function () {
                if ($this->layout and !$this->layout->hasRegion($this->region)) {
                    $this->addError('region', \Craft::t('themes', 'Theme ' . $this->layout->theme->handle. ' doesn\'t have a region ' . $this->region));
                }
            }],
            ['provider', function () {
                if (!Themes::$plugin->blockProviders->has($this->provider)) {
                    $this->addError('layout', \Craft::t('themes', 'Block provider ' . $this->provider . ' is not defined'));
                }
            }],
            ['options', function () {
                $options = $this->options;
                $strategyOptions = $this->cacheStrategyOptions;
                $errors = [];
                if (!$options->validate()) {
                    $errors = array_merge($errors, $options->getErrors());
                }
                if ($strategyOptions and !$strategyOptions->validate()) {
                    $errors = array_merge($errors, $strategyOptions->getErrors());
                }
                if ($errors) {
                    $this->addError('options', $errors);
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
        return array_merge(parent::fields(), ['handle', 'options', 'errors', 'cacheStrategyOptions']);
    }

    /**
     * @inheritDoc
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $array = parent::toArray($fields, $expand, $recursive);
        $array['options'] = array_merge($array['options'], $array['cacheStrategyOptions'] ?? []);
        unset($array['cacheStrategyOptions']);
        return $array;
    }

    /**
     * Get block cache strategy options
     * 
     * @return ?BlockCacheStrategyOptions
     */
    public function getCacheStrategyOptions(): ?BlockCacheStrategyOptions
    {
        $strategy = $this->cacheStrategy;
        if ($strategy) {
            return $strategy->options;
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): BlockOptions
    {
        if ($this->_optionsModel === null) {
            $this->_optionsModel = $this->getOptionsModel();
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
        $this->options->setAttributes($options);
        $strategy = $this->cacheStrategy;
        if ($strategy) {
            $strategy->options->setAttributes($options);
        }
    }
    
    /**
     * @inheritDoc
     */
    public function getCacheStrategy(): ?BlockCacheStrategyInterface
    {
        if ($this->options->cacheStrategy and Themes::$plugin->blockCache->hasStrategy($this->options->cacheStrategy)) {
            return Themes::$plugin->blockCache->getStrategy($this->options->cacheStrategy);
        }
        return null;
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