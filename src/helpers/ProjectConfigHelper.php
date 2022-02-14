<?php
namespace Ryssbowh\CraftThemes\helpers;

use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\services\BlockService;
use Ryssbowh\CraftThemes\services\DisplayService;
use Ryssbowh\CraftThemes\services\FieldsService;
use Ryssbowh\CraftThemes\services\GroupsService;
use Ryssbowh\CraftThemes\services\LayoutService;
use Ryssbowh\CraftThemes\services\ViewModeService;
use craft\services\Plugins;

/**
 * Helper to ensure some project config changes are applied before others
 */
class ProjectConfigHelper
{
    private static $_processedDisplays = false;
    private static $_processedLayouts = false;
    private static $_processedViewModes = false;
    private static $_processedBlocks = false;
    private static $_processedFields = [];
    private static $_processedGroups = false;

    /**
     * Ensure all displays config changes are processed immediately.
     *
     * @param bool $force Whether to proceed even if YAML changes are not currently being applied
     */
    public static function ensureAllDisplaysProcessed(bool $force = false)
    {
        $projectConfig = \Craft::$app->getProjectConfig();

        if (static::$_processedDisplays || (!$force && !$projectConfig->getIsApplyingYamlChanges())) {
            return;
        }

        static::$_processedDisplays = true;

        $allDisplays = $projectConfig->get(DisplayService::CONFIG_KEY, true) ?? [];

        foreach ($allDisplays as $uid => $data) {
            $projectConfig->processConfigChanges(DisplayService::CONFIG_KEY . '.' . $uid, false, null, $force);
        }
    }

    /**
     * Ensure all fields config changes are processed immediately.
     *
     * @param bool $force Whether to proceed even if YAML changes are not currently being applied
     */
    public static function ensureAllFieldsProcessed(bool $force = false)
    {
        $projectConfig = \Craft::$app->getProjectConfig();

        if (!$force && !$projectConfig->getIsApplyingYamlChanges()) {
            return;
        }

        $allFields = $projectConfig->get(FieldsService::CONFIG_KEY, true) ?? [];

        foreach ($allFields as $uid => $data) {
            if (in_array($uid, static::$_processedFields)) {
                continue;
            }
            $projectConfig->processConfigChanges(FieldsService::CONFIG_KEY . '.' . $uid, false, null, $force);
            static::$_processedFields[] = $uid;
        }
    }

    /**
     * Ensure some fields config changes are processed immediately.
     *
     * @param bool $force Whether to proceed even if YAML changes are not currently being applied
     */
    public static function ensureFieldsProcessed(array $fieldUids, bool $force = false)
    {
        $projectConfig = \Craft::$app->getProjectConfig();

        if (!$force && !$projectConfig->getIsApplyingYamlChanges()) {
            return;
        }

        foreach ($fieldUids as $uid) {
            if (in_array($uid, static::$_processedFields)) {
                continue;
            }
            $projectConfig->processConfigChanges(FieldsService::CONFIG_KEY . '.' . $uid, false, null, $force);
            static::$_processedFields[] = $uid;
        }
    }

    /**
     * Ensure all groups config changes are processed immediately.
     *
     * @param bool $force Whether to proceed even if YAML changes are not currently being applied
     */
    public static function ensureAllGroupsProcessed(bool $force = false)
    {
        $projectConfig = \Craft::$app->getProjectConfig();

        if (static::$_processedGroups || (!$force && !$projectConfig->getIsApplyingYamlChanges())) {
            return;
        }

        static::$_processedGroups = true;

        $allGroups = $projectConfig->get(GroupsService::CONFIG_KEY, true) ?? [];

        foreach ($allGroups as $uid => $data) {
            $projectConfig->processConfigChanges(GroupsService::CONFIG_KEY . '.' . $uid, false, null, $force);
        }
    }

    /**
     * Ensure all blocks config changes are processed immediately.
     *
     * @param bool $force Whether to proceed even if YAML changes are not currently being applied
     */
    public static function ensureAllBlocksProcessed(bool $force = false)
    {
        $projectConfig = \Craft::$app->getProjectConfig();

        if (static::$_processedBlocks || (!$force && !$projectConfig->getIsApplyingYamlChanges())) {
            return;
        }

        static::$_processedBlocks = true;

        $allBlocks = $projectConfig->get(BlockService::CONFIG_KEY, true) ?? [];

        foreach ($allBlocks as $uid => $data) {
            $projectConfig->processConfigChanges(BlockService::CONFIG_KEY . '.' . $uid, false, null, $force);
        }
    }

    /**
     * Ensure all layouts config changes are processed immediately.
     *
     * @param bool $force Whether to proceed even if YAML changes are not currently being applied
     */
    public static function ensureAllLayoutsProcessed(bool $force = false)
    {
        $projectConfig = \Craft::$app->getProjectConfig();

        if (static::$_processedLayouts || (!$force && !$projectConfig->getIsApplyingYamlChanges())) {
            return;
        }

        static::$_processedLayouts = true;

        $allLayouts = $projectConfig->get(LayoutService::CONFIG_KEY, true) ?? [];

        foreach ($allLayouts as $uid => $data) {
            $projectConfig->processConfigChanges(LayoutService::CONFIG_KEY . '.' . $uid, false, null, $force);
        }
    }

    /**
     * Ensure all view modes config changes are processed immediately.
     *
     * @param bool $force Whether to proceed even if YAML changes are not currently being applied
     */
    public static function ensureAllViewModesProcessed(bool $force = false)
    {
        $projectConfig = \Craft::$app->getProjectConfig();

        if (static::$_processedViewModes || (!$force && !$projectConfig->getIsApplyingYamlChanges())) {
            return;
        }

        static::$_processedViewModes = true;

        $allViewModes = $projectConfig->get(ViewModeService::CONFIG_KEY, true) ?? [];

        foreach ($allViewModes as $uid => $data) {
            $projectConfig->processConfigChanges(ViewModeService::CONFIG_KEY . '.' . $uid, false, null, $force);
        }
    }

    /**
     * Is a theme marked as having its data installed in project config
     *
     * @param  ThemeInterface $theme
     * @return bool
     */
    public static function isDataInstalledForTheme(ThemeInterface $theme): bool
    {
        return \Craft::$app->projectConfig->get('plugins.' . $theme->handle . '.dataInstalled', true) ?? false;
    }

    /**
     * Mark a theme as having its data installed in project config
     * 
     * @param ThemeInterface $theme
     */
    public static function markDataInstalledForTheme(ThemeInterface $theme)
    {
        \Craft::$app->projectConfig->set('plugins.' . $theme->handle . '.dataInstalled', true, null, false);
    }

    /**
     * Mark a theme as having its data not installed in project config
     * 
     * @param ThemeInterface $theme
     */
    public static function markDataNotInstalledForTheme(ThemeInterface $theme)
    {
        \Craft::$app->projectConfig->set('plugins.' . $theme->handle . '.dataInstalled', false, null, false);
    }
}