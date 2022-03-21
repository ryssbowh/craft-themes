<?php
namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\services\LayoutService;

/**
 * Custom layout, defined by the user
 */
class CustomLayout extends Layout
{
    /**
     * @inheritDoc
     */
    public function getIsCustom(): bool
    {
        return true;
    }

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
    public function getTemplatingKey(): string
    {
        return $this->elementUid;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Custom : {name}', ['name' => $this->name]);
    }
}