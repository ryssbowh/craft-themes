<?php
namespace Ryssbowh\CraftThemes\models\layouts;

use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\GroupInterface;
use Ryssbowh\CraftThemes\interfaces\RegionInterface;
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
     * @inheritDoc
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
    public function getTemplates(ViewModeInterface $viewMode): array
    {
        $key = $this->getTemplatingKey();
        $section = $this->element->section->handle;
        $type = $this->type;
        return [
            'layouts/' . $type . '_' . $key . '_' . $viewMode->handle,
            'layouts/' . $type . '_' . $section . '_' . $viewMode->handle,
            'layouts/' . $type . '_' . $key,
            'layouts/' . $type . '_' . $section,
            'layouts/' . $type,
            'layouts/layout',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getBlockTemplates(BlockInterface $block): array
    {
        $key = $this->getTemplatingKey();
        $type = $this->type;
        $section = $this->element->section->handle;
        $name = $block->machineName;
        $region = $block->region;
        return [
            'blocks/' . $type . '_' . $key . '_' . $region . '_' . $name,
            'blocks/' . $type . '_' . $section . '_' . $region  . '_' . $name,
            'blocks/' . $type . '_' . $key . '_' . $name,
            'blocks/' . $type . '_' . $section . '_' . $name,
            'blocks/' . $type . '_' . $name,
            'blocks/' . $name,
            'blocks/block',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getRegionTemplates(RegionInterface $region): array
    {
        $handle = $region->handle;
        $type = $this->type;
        $section = $this->element->section->handle;
        $key = $this->getTemplatingKey();
        return [
            'regions/' . $type . '_' . $key . '_region-' . $handle,
            'regions/' . $type . '_' . $section . '_region-' . $handle,
            'regions/' . $type . '_' . $key . '_region',
            'regions/' . $type . '_' . $section . '_region',
            'regions/' . $type . '_region-' . $handle,
            'regions/' . $type . '_region',
            'regions/region-' . $handle,
            'regions/region',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFieldTemplates(FieldInterface $field): array
    {
        $type = $this->type;
        $viewMode = $field->viewMode->handle;
        $key = $this->templatingKey;
        $displayer = $field->displayer->handle;
        $handle = $field->handle;
        $section = $this->element->section->handle;
        return [
            'fields/' . $type . '_' . $key . '_' . $viewMode . '_' . $displayer . '_' . $handle,
            'fields/' . $type . '_' . $section . '_' . $viewMode . '_' . $displayer . '_' . $handle,
            'fields/' . $type . '_' . $key . '_' . $viewMode . '_' . $displayer,
            'fields/' . $type . '_' . $section . '_' . $viewMode . '_' . $displayer,
            'fields/' . $type . '_' . $key . '_' . $displayer . '_' . $handle,
            'fields/' . $type . '_' . $section . '_' . $displayer . '_' . $handle,
            'fields/' . $type . '_' . $key . '_' . $displayer,
            'fields/' . $type . '_' . $section . '_' . $displayer,
            'fields/' . $type . '_' . $displayer . '_' . $handle,
            'fields/' . $type . '_' . $displayer,
            'fields/' . $displayer . '_' . $handle,
            'fields/' . $displayer
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFileTemplates(FieldInterface $field, FileDisplayerInterface $displayer): array
    {
        $type = $this->type;
        $viewMode = $field->viewMode->handle;
        $key = $this->templatingKey;
        $displayer = $displayer->handle;
        $handle = $field->handle;
        $section = $this->element->section->handle;
        return [
            'files/' . $type . '_' . $key . '_' . $viewMode . '_' . $displayer . '_' . $handle,
            'files/' . $type . '_' . $section . '_' . $viewMode . '_' . $displayer . '_' . $handle,
            'files/' . $type . '_' . $key . '_' . $viewMode . '_' . $displayer,
            'files/' . $type . '_' . $section . '_' . $viewMode . '_' . $displayer,
            'files/' . $type . '_' . $key . '_' . $displayer . '_' . $handle,
            'files/' . $type . '_' . $section . '_' . $displayer . '_' . $handle,
            'files/' . $type . '_' . $key . '_' . $displayer,
            'files/' . $type . '_' . $section . '_' . $displayer,
            'files/' . $type . '_' . $displayer . '_' . $handle,
            'files/' . $type . '_' . $displayer,
            'files/' . $displayer . '_' . $handle,
            'files/' . $displayer
        ];
    }

    /**
     * @inheritDoc
     */
    public function getGroupTemplates(GroupInterface $group): array
    {
        $type = $this->type;
        $key = $this->templatingKey;
        $viewMode = $group->viewMode->handle;
        $section = $this->element->section->handle;
        $handle = $group->handle;
        return [
            'groups/' . $type . '_' . $key . '_' . $viewMode . '_group-' . $handle,
            'groups/' . $type . '_' . $section . '_' . $viewMode . '_group-' . $handle,
            'groups/' . $type . '_' . $key . '_' . $viewMode . '_group',
            'groups/' . $type . '_' . $section . '_' . $viewMode . '_group',
            'groups/' . $type . '_' . $key . '_group-' . $handle,
            'groups/' . $type . '_' . $section . '_group-' . $handle,
            'groups/' . $type . '_' . $key . '_group',
            'groups/' . $type . '_' . $section . '_group',
            'groups/' . $type . '_group-' . $handle,
            'groups/' . $type . '_group',
            'groups/group-' . $handle,
            'groups/group'
        ];
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