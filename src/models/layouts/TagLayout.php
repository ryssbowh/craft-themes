<?php
namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\services\LayoutService;
use Ryssbowh\CraftThemes\traits\ElementLayout;

/**
 * A layout associated to a tag group and a theme
 */
class TagLayout extends Layout
{
    use ElementLayout;

    /**
     * @var string
     */
    protected $_type = LayoutService::TAG_HANDLE;

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['elementUid', 'required'],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Tag : {name}', ['name' => $this->element->name]);
    }

    /**
     * @inheritDoc
     */
    public function canHaveBlocks(): bool
    {
        return false;
    }
    
    /**
     * @inheritDoc
     */
    protected function loadElement()
    {
        return \Craft::$app->tags->getTagGroupByUid($this->elementUid);
    }
}