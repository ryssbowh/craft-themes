<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\helpers\ElementLayoutTrait;
use Ryssbowh\CraftThemes\services\LayoutService;

/**
 * A layout associated to a global set and a theme
 */
class GlobalLayout extends Layout
{
    use ElementLayoutTrait;

    /**
     * @var string
     */
    protected $_type = LayoutService::GLOBAL_HANDLE;

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
    public function canHaveBlocks(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Global : {name}', ['name' => $this->element->name]);
    }

    /**
     * @inheritDoc
     */
    protected function loadElement()
    {
        foreach (\Craft::$app->globals->getAllSets() as $set) {
            if ($set->uid == $this->elementUid) {
                return $set;
            }
        }
        return null;
    }
}