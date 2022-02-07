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
use Ryssbowh\CraftThemes\services\LayoutService;
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
     * @var DateTime
     */
    public $dateCreated;

    /**
     * @var DateTime
     */
    public $dateUpdated;

    /**
     * @var BlockOptions
     */
    protected $_optionsModel;

    /**
     * @var string
     */
    protected $_cacheStrategyHandle;

    /**
     * @var array
     */
    protected $_cacheStrategyOptions = [];

    /**
     * @var CacheStrategyInterface
     */
    protected $_cacheStrategy;

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
        return [
            'layout_id' => $this->layout->uid,
            'region' => $this->region,
            'handle' => $this->handle,
            'provider' => $this->provider,
            'order' => $this->order,
            'active' => (bool)$this->active,
            'options' => $this->options->getConfig(),
            'cacheStrategy' => $this->cacheStrategy ? [
                'handle' => $this->cacheStrategy->handle,
                'options' => $this->cacheStrategy->options->getConfig()
            ] : []
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
    public function getLayout(): ?LayoutInterface
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
            [['uid', 'id', 'dateUpdated', 'dateCreated'], 'safe'],
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
                if ($cacheStrategy = $this->cacheStrategy) {
                    $cacheStrategy->options->validate();    
                }
            }]
        ];
    }

    /**
     * @inheritDoc
     */
    public function hasErrors($attribute = null)
    {
        if ($attribute == 'options') {
            return $this->options->hasErrors();
        }
        $strategy = $this->cacheStrategy;
        if ($attribute == 'cacheStrategy') {
            return $strategy ? $strategy->options->hasErrors() : false;
        }
        if ($attribute !== null) {
            return parent::hasErrors($attribute);    
        }
        if ($this->options->hasErrors()) {
            return true;
        }
        $strategy = $this->cacheStrategy;
        if ($strategy and $strategy->options->hasErrors()) {
            return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getErrors($attribute = null)
    {
        if ($attribute == 'options') {
            return $this->options->errors;
        }
        $strategy = $this->cacheStrategy;
        if ($attribute == 'cacheStrategy') {
            return $strategy ? $strategy->options->errors : [];
        }
        if ($attribute !== null) {
            return parent::getErrors($attribute);
        }
        $errors = parent::getErrors();
        if ($errors2 = $this->options->errors) {
            $errors['options'] = $errors2;
        }
        if ($strategy and $errors2 = $strategy->options->errors) {
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
        return array_merge(parent::fields(), ['name', 'handle', 'options', 'errors', 'smallDescription', 'longDescription', 'canBeCached']);
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): BlockOptions
    {
        if ($this->_optionsModel === null) {
            $class = $this->getOptionsModel();
            $this->_optionsModel = new $class([
                'block' => $this
            ]);
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
        $this->options->setValues($options);
    }

    /**
     * @inheritDoc
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $array = parent::toArray($fields, $expand, $recursive);
        $array['options'] = $this->options->values;
        $array['optionsDefinitions'] = $this->options->definitions;
        $array['optionsDefault'] = $this->options->defaultValues;
        $array['cacheStrategy'] = [
            'handle' => $this->cacheStrategy ? $this->cacheStrategy->handle : '',
            'options' => $this->cacheStrategy ? $this->cacheStrategy->options->values : []
        ];
        return $array;
    }

    /**
     * @inheritDoc
     */
    public function getCanBeCached(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function setCacheStrategy($strategy)
    {
        if (is_string($strategy)) {
            $strategy = json_decode($strategy, true);
        }
        $this->_cacheStrategy = null;
        $this->_cacheStrategyHandle = $strategy['handle'] ?? null;
        $this->_cacheStrategyOptions = $strategy['options'] ?? [];
    }
    
    /**
     * @inheritDoc
     */
    public function getCacheStrategy(): ?BlockCacheStrategyInterface
    {
        if ($this->_cacheStrategy === null) {
            if ($this->_cacheStrategyHandle and Themes::$plugin->blockCache->hasStrategy($this->_cacheStrategyHandle)) {
                $this->_cacheStrategy = Themes::$plugin->blockCache->getStrategy($this->_cacheStrategyHandle);
                $this->_cacheStrategy->options->setValues($this->_cacheStrategyOptions);
            } else {
                $this->_cacheStrategy = false;
            }
        }
        return $this->_cacheStrategy ?: null;
    }

    /**
     * @inheritDoc
     */
    public function afterSave()
    {
        $record = Themes::$plugin->blocks->getRecordByUid($this->uid);
        $options = $this->options->values;
        foreach ($this->options->definitions as  $name => $definition) {
            $save = $definition['saveInConfig'] ?? true;
            if (!$save) {
                $options[$name] = $this->options->$name;
            }
        }
        $record->options = $options;
        $record->save(false);
    }

    /**
     * @inheritDoc
     */
    public function getTemplates(LayoutInterface $layout): array
    {
        return $layout->getBlockTemplates($this);
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return Themes::$plugin->view->renderBlock($this);
    }

    /**
     * Model class that defines this block's options 
     * 
     * @return string
     */
    abstract protected function getOptionsModel(): string;
}