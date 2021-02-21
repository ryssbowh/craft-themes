<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\exceptions\BlockException;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\SuggestsTemplates;
use Ryssbowh\CraftThemes\models\NoOptions;
use craft\base\Model;
use zz\Html\HTMLMinify;

abstract class Block extends Model implements BlockInterface, SuggestsTemplates
{
	public static $handle;
	public $name = '';
    public $smallDescription = '';
	public $hasOptions = false;
    private $_options = [];

	public $id;
	public $region;
    public $layout;
	public $provider;
	public $order;
	public $active;
	public $options = [];
    public $dateCreated;
    public $dateUpdated;
    public $uid;

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
            'layout' => $this->layout()->uid,
			'region' => $this->region,
			'handle' => $this->handle,
			'provider' => $this->provider,
            'order' => $this->order,
			'active' => $this->active,
            'options' => $this->options
		];
	}

    public function layout(): Layout
    {
        return Themes::$plugin->layouts->getById($this->layout);
    }

	public function rules()
	{
		return [
			[['region', 'handle', 'provider', 'order', 'active', 'layout'], 'required'],
			[['region', 'handle', 'provider'], 'string'],
			['active', 'boolean'],
			[['order', 'layout'], 'number'],
            ['options', function(){}]
		];
	}

	public function getProvider(): BlockProviderInterface
	{
		return Themes::$plugin->blockProviders->getByHandle($this->provider);
	}

	public function getHandle(): string
	{
		return $this::$handle;
	}

	public function getMachineName(): string
	{
		return $this->provider . '_' . $this::$handle;
	}

	public function getOptionsHtml(): string
	{
		return '';
	}

	public function getTemplateSuggestions(): array
	{
		return ['blocks/block-' . $this->getMachineName(), 'blocks/block'];
	}

	public function fields()
	{
		return array_merge(parent::fields(), ['handle', 'hasOptions', 'optionsHtml']);
	}

    public function getOptions(): Model
    {
        return \Yii::configure($this->getOptionsModel(), $this->_options);
    }

    public function setOptions($options)
    {
        $this->options = $options;
        if (is_string($options)) {
            $options = json_decode($options, true);
        } elseif ($options instanceof Model) {
            $options = $options->getAttributes();
        }
        $this->_options = $options;
    }

    public function getOptionsModel(): Model
    {
        return new NoOptions;
    }
}