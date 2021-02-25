<?php 

namespace Ryssbowh\CraftThemes\models;

use Ryssbowh\CraftThemes\interfaces\RenderableInterface;
use craft\base\Model;

class Region extends Model implements RenderableInterface
{   
    /**
     * @var string
     */
    public $handle = '';

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $width = '100%';

    /**
     * @var array
     */
    public $blocks = [];

    /**
     * @inheritDoc
     */
    public function getTemplateSuggestions(): array
    {
        return ['regions/region-' . $this->handle, 'regions/region'];
    }
}