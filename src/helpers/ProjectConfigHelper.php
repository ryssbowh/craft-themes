<?php
namespace Ryssbowh\CraftThemes\helpers;

use Ryssbowh\CraftThemes\services\DisplayService;
use Ryssbowh\CraftThemes\services\LayoutService;
use Ryssbowh\CraftThemes\services\ViewModeService;

/**
 * Helper to ensure some project config changes are applied before others
 */
class ProjectConfigHelper
{
    private static $_processedDisplays = false;
    private static $_processedLayouts = false;
    private static $_processedViewModes = false;

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
}