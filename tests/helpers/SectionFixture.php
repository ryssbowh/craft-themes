<?php 

namespace Ryssbowh\CraftThemesTests\helpers;

use craft\base\Field;
use craft\db\Query;
use craft\db\Table;
use craft\fieldlayoutelements\CustomField;
use craft\models\EntryType;
use craft\models\Section;
use craft\models\Section_SiteSettings;
use craft\test\DbFixtureTrait;
use yii\test\DbFixture;
use yii\test\FileFixtureTrait;

abstract class SectionFixture extends DbFixture
{
    use FileFixtureTrait;
    use DbFixtureTrait;

    private $_sections = [];

    public function load()
    {
        codecept_debug('Loading sections');
        foreach ($this->loadData($this->dataFile) as $key => $data) {
            $section = $this->createSection($data);

            if (!$this->saveSection($section)) {
                throw new \Exception(implode(' ', $section->getErrorSummary(true)));
            }

            $this->_sections[$key] = $section;
        }
    }

    public function unload()
    {
        codecept_debug('Unloading sections');
        foreach ($this->_sections as $key => $section) {
            $this->deleteSection($key);
        }
        $this->hardDelete();
    }

    public function deleteSection($key)
    {
        if (isset($this->_sections[$key])) {
            $section = $this->_sections[$key];
            unset($this->_sections[$key]);
            return \Craft::$app->sections->deleteSection($section);
        }
    }

    public function getSection(string $key): ?Section
    {
        return $this->_sections[$key] ?? null;
    }

    public function addFieldToEntryType(Field $field, EntryType $entryType)
    {
        $layout = $entryType->fieldLayout;
        $tab = $layout->tabs[0];
        $fields = $layout->fields;
        $fields[] = $field;
        $layout->fields = $fields;
        $tab->elements[] = \Craft::$app->fields->createLayoutElement([
            'type' => CustomField::class,
            'fieldUid' => $field->uid
        ]);
        if (!\Craft::$app->sections->saveEntryType($entryType)) {
            throw new \Exception(implode(' ', $entryType->getErrorSummary(true)));
        }
    }

    // public function addFieldToEntryType(Field $field, EntryType $entryType)
    // {
    //     $layout = $entryType->fieldLayout;
    //     $tabs = $layout->tabs;
    //     $postedFieldLayout = array();

    //     foreach ($tabs as $tab) {
    //         $fields = $tab->getFields();
    //         foreach ($fields as $field2) {
    //             $postedFieldLayout[$tab->name][] = $field2->fieldId;
    //         }
    //         $postedFieldLayout[$tab->name][] = $field->id;
    //     }

    //     $fieldLayout = \Craft::$app->fields->assembleLayout($postedFieldLayout);
    //     $fieldLayout->type = 'craft\elements\Entry';
    //     $entryType->setFieldLayout($fieldLayout);

    //     if (!\Craft::$app->sections->saveEntryType($entryType)) {
    //         throw new \Exception(implode(' ', $entryType->getErrorSummary(true)));
    //     }
    // }

    protected function createSection(array $data): Section
    {
        $allSiteSettings = [];
        foreach (\Craft::$app->getSites()->getAllSites() as $site) {
            $siteSettings = new Section_SiteSettings();
            $siteSettings->siteId = $site->id;
            $allSiteSettings[$site->id] = $siteSettings;
        }
        $data['siteSettings'] = $allSiteSettings;
        return new Section($data);
    }

    protected function saveSection(Section $section)
    {
        return \Craft::$app->sections->saveSection($section);
    }
}