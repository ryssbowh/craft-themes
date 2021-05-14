<?php

namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\services\LayoutService;
use craft\elements\User;
use craft\models\FieldLayout;

class VolumeLayout extends Layout
{
    /**
     * @var string
     */
    public $type = LayoutService::VOLUME_HANDLE;

    public function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            ['element', 'required'],
        ]);
    }
    
    /**
     * @var boolean
     */
    protected $_hasDisplays = true;

    /**
     * @inheritDoc
     */
    protected function loadElement()
    {
        return \Craft::$app->volumes->getVolumeByUid($this->element);
    }

    public function hasDisplays(): bool
    {
        return true;
    }

    public function canHaveUrls(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Asset : {name}', ['name' => $this->element()->name]);
    }

    /**
     * @inheritDoc
     */
    public function getHandle(): string
    {
        return $this->type . '_' . $this->element;
    }

    public function getFieldLayout(): FieldLayout
    {
        return $this->element()->getFieldLayout();
    }
}