<?php
namespace Ryssbowh\CraftThemes\helpers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\fields\Assets;
use craft\fields\Categories;
use craft\fields\Entries;
use craft\fields\Tags;
use craft\fields\Users;

class ViewModesHelper
{
    /**
     * Get view modes available for an Entries field and a theme
     * 
     * @param  Entries        $field
     * @param  ThemeInterface $theme
     * @return array
     */
    public static function getSectionsViewModes(Entries $field, ThemeInterface $theme): array
    {
        $sources = $field->sources;
        if ($sources == '*') {
            return static::getAllSectionsViewModes($theme);
        }
        $viewModes = [];
        foreach ($sources as $source) {
            if ($source == 'singles') {
                $viewModes = $viewModes + static::getSingleEntriesViewModes($theme);
            } else {
                $elems = explode(':', $source);
                $viewModes = $viewModes + static::getSectionViewModes($theme, $elems[1]);
            }
        }
        return $viewModes;
    }

    /**
     * Get view modes for the user photo volume
     * 
     * @param  ThemeInterface $theme
     * @return array
     */
    public static function getUserPhotoViewModes(ThemeInterface $theme): array
    {
        $volumeUid = \Craft::$app->getProjectConfig()->get('users.photoVolumeUid');
        $volume = \Craft::$app->volumes->getVolumeByUid($volumeUid);
        $viewModes = [$volume->uid => [
            'label' => $volume->name,
            'viewModes' => []
        ]];
        if ($layout = $volume->getLayout($theme)) {
            foreach ($layout->viewModes as $viewMode) {
                $viewModes[$volume->uid]['viewModes'][$viewMode->uid] = $viewMode->name;
            }
        }
        return $viewModes;
    }

    /**
     * Get view modes available for an Assets field and a theme
     * 
     * @param  Assets         $field
     * @param  ThemeInterface $theme
     * @return array
     */
    public static function getVolumesViewModes(Assets $field, ThemeInterface $theme): array
    {
        $viewModes = [];
        foreach (static::getAllVolumes($field) as $volume) {
            if (!$volume) {
                continue;
            }
            if (!$layout = $volume->getLayout($theme)) {
                continue;
            }
            $volumeViewModes = [];
            foreach ($layout->viewModes as $viewMode) {
                $volumeViewModes[$viewMode->uid] = $viewMode->name;
            }
            $viewModes[$volume->uid] = [
                'label' => $volume->name,
                'viewModes' => $volumeViewModes
            ];
        }
        return $viewModes;
    }

    /**
     * Get view modes available for a Categories field and a theme
     * 
     * @param  Categories     $field
     * @param  ThemeInterface $theme
     * @return array
     */
    public static function getCategoryGroupViewModes(Categories $field, ThemeInterface $theme): array
    {
        $viewModes = [];
        $elems = explode(':', $field->source);
        $group = \Craft::$app->categories->getGroupByUid($elems[1]);
        if ($group) {
            if (!$layout = $group->getLayout($theme)) {
                return [];
            }
            foreach ($layout->viewModes as $viewMode) {
                $viewModes[$viewMode->uid] = $viewMode->name;
            }
        }
        return $viewModes;
    }

    /**
     * Get view modes available for a Tag field and a theme
     * 
     * @param  Tags           $field
     * @param  ThemeInterface $theme
     * @return array
     */
    public static function getTagGroupViewModes(Tags $field, ThemeInterface $theme): array
    {
        $viewModes = [];
        $elems = explode(':', $field->source);
        $group = \Craft::$app->tags->getTagGroupByUid($elems[1]);
        if ($group) {
            if (!$layout = $group->getLayout($theme)) {
                return [];
            }
            foreach ($layout->viewModes as $viewMode) {
                $viewModes[$viewMode->uid] = $viewMode->name;
            }
        }
        return $viewModes;
    }

    /**
     * Get users view modes available for a theme
     * 
     * @param  ThemeInterface $theme
     * @return array
     */
    public static function getUserViewModes(ThemeInterface $theme): array
    {
        $layout = Themes::$plugin->layouts->get($theme, LayoutService::USER_HANDLE);
        if (!$layout) {
            return [];
        }
        $viewModes = [];
        foreach ($layout->getViewModes() as $viewMode) {
            $viewModes[$viewMode->uid] = $viewMode->name;
        }
        return $viewModes;
    }

    /**
     * Get all defined volumes on a field
     *
     * @param  Assets $field
     * @return array
     */
    protected static function getAllVolumes(Assets $field): array
    {
        $source = $field->sources;
        if ($source == '*') {
            return \Craft::$app->volumes->getAllVolumes();
        } else {
            $volumes = [];
            foreach ($source as $source) {
                $elems = explode(':', $source);
                $volumes[] = \Craft::$app->volumes->getVolumeByUid($elems[1]);
            }
            return $volumes;
        }
    }

    /**
     * Get all view modes defined for all sections
     *
     * @param  ThemeInterface $theme
     * @return array
     */
    protected static function getAllSectionsViewModes(ThemeInterface $theme): array
    {
        $sections = \Craft::$app->sections->getAllSections();
        $viewModes = [];
        foreach ($sections as $section) {
            foreach ($section->getEntryTypes() as $type) {
                $layout = $type->getLayout($theme);
                if (!$layout) {
                    continue;
                }
                $viewModes2 = [];
                foreach ($layout->getViewModes() as $viewMode) {
                    $viewModes2[$viewMode->uid] = $viewMode->name;
                }
                $viewModes[$type->uid] = [
                    'label' => $section->name . ' : ' . $type->name,
                    'viewModes' => $viewModes2
                ];
            }
        }
        return $viewModes;
    }

    /**
     * Get all view modes defined for single sections
     *
     * @param  ThemeInterface $theme
     * @return array
     */
    protected static function getSingleEntriesViewModes(ThemeInterface $theme): array
    {
        $sections = \Craft::$app->sections->getSectionsByType('single');
        $viewModes = [];
        foreach ($sections as $section) {
            $type = $section->getEntryTypes()[0];
            $layout = $type->getLayout($theme);
            if (!$layout) {
                continue;
            }
            $viewModes2 = [];
            foreach ($layout->getViewModes() as $viewMode) {
                $viewModes2[$viewMode->uid] = $viewMode->name;
            }
            $viewModes[$type->uid] = [
                'label' => $section->name . ' : ' . $type->name,
                'viewModes' => $viewModes2
            ];
        }
        return $viewModes;
    }

    /**
     * Get all view modes defined for one section
     *
     * @param  ThemeInterface $theme
     * @param  string $uid
     * @return array
     */
    protected static function getSectionViewModes(ThemeInterface $theme, string $uid): array
    {
        $section = \Craft::$app->sections->getSectionByUid($uid);
        $types = [];
        if ($section) {
            $type = $section->getEntryTypes()[0];
            $layout = $type->getLayout($theme);
            if (!$layout) {
                return [];
            }
            $viewModes = [];
            foreach ($layout->getViewModes() as $viewMode) {
                $viewModes[$viewMode->handle] = $viewMode->name;
            }
            $types[] = [$type->uid => [
                'label' => $section->name . ' : ' . $type->name,
                'viewModes' => $viewModes
            ]];
        }
        return $types;
    }
}