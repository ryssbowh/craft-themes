<?php
namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\services\LayoutService;
use Ryssbowh\CraftThemes\traits\ElementLayout;

/**
 * A layout associated to a entry type and a theme
 */
class EntryLayout extends Layout
{
    use ElementLayout;

    /**
     * @var string
     */
    protected $_type = LayoutService::ENTRY_HANDLE;

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
        return $this->element->section->handle . '-' . $this->element->handle;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return \Craft::t('themes', 'Section {section} : {name}', ['section' => $this->element->section->name, 'name' => $this->element->name]);
    }

    /**
     * @inheritDoc
     */
    protected function loadElement()
    {
        foreach (\Craft::$app->sections->getAllEntryTypes() as $entryType) {
            if ($entryType->uid == $this->elementUid) {
                return $entryType;
            }
        }
        return null;
    }
}