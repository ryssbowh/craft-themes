<?php
namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\BlockException;
use Ryssbowh\CraftThemes\interfaces\BlockCacheStrategyInterface;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\BlockOptionsInterface;
use Ryssbowh\CraftThemes\interfaces\BlockProviderInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\RegionInterface;
use Ryssbowh\CraftThemes\models\BlockOptions;
use Ryssbowh\CraftThemes\models\blockCacheOptions\BlockCacheStrategyOptions;
use craft\base\Element;
use craft\base\Model;

/**
 * Base class for all blocks
 */
abstract class Block extends Model implements BlockInterface
{
    /**
     * @var string
     */
    public static $handle;

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
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        $options = $this->options->getConfig();
        $strategyOptions = $this->getCacheStrategyOptions();
        if ($strategyOptions) {
            $options = array_merge($options, ['cacheStrategyOptions' => $strategyOptions->getConfig()]);
        }
        return [
            'layout_id' => $this->layout->uid,
            'region' => $this->region,
            'handle' => $this->handle,
            'provider' => $this->provider,
            'order' => $this->order,
            'active' => (bool)$this->active,
            'options' => $options
        ];
    }

    /**
     * @inheritDoc
     */
    public function isVisible(): bool
    {
        return $this->active;
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
            ['active', 'boolean', 'trueValue' => true, 'falseValue' => false],
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
                $this->options->validate();
            }],
            ['cacheStrategy', function () {
                if ($cacheStrategyOptions = $this->cacheStrategyOptions) {
                    $cacheStrategyOptions->validate();    
                }
            }]
        ];
    }

    /**
     * @inheritDoc
     */
    public function hasErrors($attribute = null)
    {
        if ($attribute !== null) {
            return parent::hasErrors($attribute);    
        }
        if ($this->options->hasErrors()) {
            return true;
        }
        $strategyOptions = $this->cacheStrategyOptions;
        if ($strategyOptions and $strategyOptions->hasErrors()) {
            return true;
        }
        return parent::hasErrors($attribute);
    }

    /**
     * @inheritDoc
     */
    public function getErrors($attribute = null)
    {
        if ($attribute == 'options') {
            return $this->options->errors;
        }
        $strategyOptions = $this->cacheStrategyOptions;
        if ($attribute == 'cacheStrategy') {
            return $strategyOptions ? $strategyOptions->errors : [];
        }
        if ($attribute !== null) {
            return parent::getErrors($attribute);
        }
        $errors = parent::getErrors();
        if ($errors2 = $this->options->errors) {
            $errors['options'] = $errors2;
        }
        if ($strategyOptions and $errors2 = $strategyOptions->errors) {
            $errors['cacheStrategy'] = $errors2;
        }
        return $errors;
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
    public function getLongDescription(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getMachineName(): string
    {
        return $this->provider . '-' . $this::$handle;
    }

    /**
     * @inheritDoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), ['name', 'handle', 'options', 'errors', 'smallDescription', 'longDescription']);
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
    public function getOptions(): BlockOptionsInterface
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
        $cacheStrategyOptions = $options['cacheStrategyOptions'] ?? [];
        unset($options['cacheStrategyOptions']);
        $this->options->setAttributes($options);
        $strategy = $this->cacheStrategy;
        if ($strategy) {
            $strategy->options->setAttributes($cacheStrategyOptions);
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
    public function getOptionsModel(): BlockOptionsInterface
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
    public function getTemplates(LayoutInterface $layout, RegionInterface $region): array
    {
        $type = $layout->type;
        return [
            'blocks/' . $type . '/' . $layout->getTemplatingKey() . '/' . $region->handle . '/' . $this->machineName,
            'blocks/' . $type . '/' . $layout->getTemplatingKey() . '/' . $this->machineName,
            'blocks/' . $type . '/' . $this->machineName,
            'blocks/' . $this->machineName, 
            'blocks/block'
        ];
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return Themes::$plugin->view->renderBlock($this);
    }
}