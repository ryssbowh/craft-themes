<?php 

namespace Ryssbowh\CraftThemes\models\fields;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\FieldDisplayerInterface;
use craft\base\Element;

class Title extends Field
{
    public function getHandle(): string
    {
        return 'title';
    }

    public function getAvailableDisplayers(): array
    {
        return Themes::$plugin->fieldDisplayers->getForField(self::class);
    }

    public function getName(): string
    {
        return \Craft::t('themes', 'Title');
    }

    // public function getType(): string
    // {
    //     return \Craft::t('themes', 'Title');
    // }
}