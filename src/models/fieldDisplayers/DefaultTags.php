<?php 

namespace Ryssbowh\CraftThemes\models\fieldDisplayers;

use Ryssbowh\CraftThemes\models\FieldDisplayer;
use craft\base\Model;
use craft\fields\Tags;

class DefaultTags extends FieldDisplayer
{
    public $handle = 'tags_default';

    public $isDefault = true;

    public function getName(): string
    {
        return \Craft::t('themes', 'Default');
    }

    public function getFieldTarget(): String
    {
        return Tags::class;
    }
}