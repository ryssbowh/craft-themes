<?php
namespace Ryssbowh\CraftThemes\helpers;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\interfaces\DisplayInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\FileFieldDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\GroupInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\ThemeInterface;
use Ryssbowh\CraftThemes\services\DisplayService;
use craft\helpers\FileHelper;
use craft\helpers\StringHelper;

/**
 * Helper related to themes templating
 *
 * @since 4.2.0
 */
class Templates
{
    /**
     * Get all the templates that a theme overriddes
     * 
     * @param  ThemeInterface $theme
     * @return array
     */
    public static function getOverriddenForTheme(ThemeInterface $theme): array
    {
        $baseTemplates = [];
        if (Themes::$plugin->is(Themes::EDITION_PRO)) {
            $baseTemplates = static::getAvailableProTemplates($theme);
        }
        $templates = [];
        static::checkBaseTemplates($templates, $baseTemplates, $theme);
        $parent = $theme->parent;
        while ($parent) {
            $baseTemplates = static::getThemeTemplates($parent);
            if (Themes::$plugin->is(Themes::EDITION_PRO)) {
                $baseTemplates = array_merge($baseTemplates, static::getAvailableProTemplates($theme));
            }
            static::checkBaseTemplates($templates, $baseTemplates, $theme, $parent);
            $theme = $parent;
            $parent = $parent->parent;
        }
        self::sortTemplates($templates);
        return $templates;
    }

    /**
     * Is a template overridden by a theme.
     * Returns the theme that overriddes it (it could be a parent theme), or null
     * 
     * @param  string         $template
     * @param  ThemeInterface $theme
     * @return ?ThemeInterface
     */
    public static function isTemplateOverridden(string $template, ThemeInterface $theme): ?ThemeInterface
    {
        $path = $theme->basePath . DIRECTORY_SEPARATOR . $theme->templatesFolder . DIRECTORY_SEPARATOR . $template;
        if (file_exists($path . '.twig') or file_exists($path . '.html')) {
            return $theme;
        }
        if ($parent = $theme->parent) {
            return static::isTemplateOverridden($template, $parent);
        }
        return null;
    }

    public static function getAvailableProTemplates(ThemeInterface $theme): array
    {
        return array_merge(
            static::getBlocksBaseTemplates($theme),
            static::getFieldDisplayersBaseTemplates($theme),
            static::getFileDisplayersBaseTemplates($theme),
            static::getGroupsBaseTemplates($theme),
            static::getLayoutsBaseTemplates($theme),
            static::getRegionsBaseTemplates($theme),
            [
                'themed_page' => [
                    'source' => \Craft::t('themes', 'System')
                ],
                'macros' => [
                    'source' => \Craft::t('themes', 'System')
                ],
                'regions' => [
                    'source' => \Craft::t('themes', 'System')
                ]
            ]
        );
    }

    /**
     * Get all possible templates for a theme
     * 
     * @param  ThemeInterface $theme
     * @return array
     */
    public static function getThemeTemplates(ThemeInterface $theme): array
    {
        $path = $theme->basePath . DIRECTORY_SEPARATOR . $theme->templatesFolder;
        $templates = [];
        foreach (FileHelper::findFiles($path) as $template) {
            $template = StringHelper::replaceBeginning($template, $path . DIRECTORY_SEPARATOR, '');
            $templates[substr($template, 0, -5)] = [
                'source' => $theme->name
            ];
        }
        return $templates;
    }

    /**
     * Sort an array of templates by name ascendant
     * 
     * @param array &$array
     */
    protected static function sortTemplates(array &$array)
    {
        foreach ($array as $key => &$sub) {
            if ($sub['type'] == 'folder') {
                self::sortTemplates($sub['children']);
            }
        }
        ksort($array);
    }

    /**
     * For an array of base templates, check if a theme overrides them, add it to &$templates if so.
     * If a $themeFrom is given and the template is overridden in that $themeFrom it will not add it to the array.
     * 
     * @param array               &$templates
     * @param array               $baseTemplates
     * @param ThemeInterface      $theme
     * @param ThemeInterface|null $themeFrom
     */
    protected static function checkBaseTemplates(array &$templates, array $baseTemplates, ThemeInterface $theme, ?ThemeInterface $themeFrom = null)
    {
        foreach ($baseTemplates as $template => $details) {
            $overriddenBy = static::isTemplateOverridden($template, $theme);
            if (!$overriddenBy) {
                continue;
            }
            if ($themeFrom and $themeFrom->handle == $overriddenBy->handle) {
                continue;
            }
            $elems = explode(DIRECTORY_SEPARATOR, $template);
            $name = $elems[sizeof($elems) - 1];
            unset($elems[sizeof($elems) - 1]);
            $temp = &$templates;
            foreach ($elems as $folder) {
                if (!isset($temp[$folder])) {
                    $temp[$folder] = [
                        'type' => 'folder',
                        'name' => $folder,
                        'children' => []
                    ];
                }
                $temp = &$temp[$folder]['children'];
            }
            $temp[$name] = [
                'name' => $name,
                'type' => 'template',
                'overriddenBy' => $overriddenBy->name,
                'source' => $details['source']
            ];
            unset($temp);
        }
    }

    /**
     * Get all possible block templates
     * 
     * @param  ThemeInterface $theme
     * @return array
     */
    protected static function getBlocksBaseTemplates(ThemeInterface $theme): array
    {
        $templates = [];
        foreach (Themes::$plugin->blockProviders->all as $provider) {
            foreach (Themes::$plugin->layouts->getForTheme($theme) as $layout) {
                foreach ($provider->blocks as $block) {
                    foreach ($layout->getBlockTemplates($block) as $template) {
                        $templates[$template] = [
                            'source' => \Craft::t('themes', 'System')
                        ];
                    }
                }
            }    
        }
        return $templates;
    }

    /**
     * Get all possible group templates
     * 
     * @param  ThemeInterface $theme
     * @return array
     */
    protected static function getGroupsBaseTemplates(ThemeInterface $theme): array
    {
        $templates = [];
        foreach (Themes::$plugin->layouts->getForTheme($theme) as $layout) {
            foreach ($layout->viewModes as $viewMode) {
                foreach ($viewMode->displays as $display) {
                    if ($display->type == DisplayService::TYPE_GROUP) {
                        try {
                            $display->item;
                        } catch (\Throwable $e) {
                            continue;
                        }
                        foreach ($layout->getGroupTemplates($display->item) as $template) {
                            $templates[$template] = [
                                'source' => \Craft::t('themes', 'System')
                            ];
                        }
                    }
                }
            }    
        }
        return $templates;
    }

    /**
     * Get all possible layout templates
     * 
     * @param  ThemeInterface $theme
     * @return array
     */
    protected static function getLayoutsBaseTemplates(ThemeInterface $theme)
    {
        $templates = [];
        foreach (Themes::$plugin->layouts->getForTheme($theme) as $layout) {
            foreach ($layout->viewModes as $viewMode) {
                foreach ($layout->getTemplates($viewMode) as $template) {
                    $templates[$template] = [
                        'source' => \Craft::t('themes', 'System')
                    ];
                }
            }
        }
        return $templates;
    }

    /**
     * Get all possible region templates
     * 
     * @param  ThemeInterface $theme
     * @return array
     */
    protected static function getRegionsBaseTemplates(ThemeInterface $theme)
    {
        $templates = [];
        foreach (Themes::$plugin->layouts->getForTheme($theme) as $layout) {
            foreach ($layout->regions as $region) {
                foreach ($layout->getRegionTemplates($region) as $template) {
                    $templates[$template] = [
                        'source' => \Craft::t('themes', 'System')
                    ];
                }
            }
        }
        return $templates;
    }

    /**
     * Get all possible field templates
     * 
     * @param  ThemeInterface $theme
     * @return array
     */
    protected static function getFieldDisplayersBaseTemplates(ThemeInterface $theme): array
    {
        $templates = [];
        foreach (Themes::$plugin->layouts->getForTheme($theme) as $layout) {
            foreach ($layout->viewModes as $viewMode) {
                foreach ($viewMode->displays as $display) {
                    try {
                        $display->item;
                    } catch (\Throwable $e) {
                        continue;
                    }
                    if ($display->type == DisplayService::TYPE_FIELD) {
                        foreach (static::getFieldBaseTemplates($display, $layout) as $template) {
                            $templates[$template] = [
                                'source' => \Craft::t('themes', 'System')
                            ];
                        }
                    } else {
                        foreach ($display->item->displays as $groupDisplay) {
                            foreach (static::getFieldBaseTemplates($groupDisplay, $layout) as $template) {
                                $templates[$template] = [
                                    'source' => \Craft::t('themes', 'System')
                                ];
                            }
                        }
                    }
                }
            }    
        }
        return $templates;
    }

    /**
     * Get all possible file templates
     * 
     * @param  ThemeInterface $theme
     * @return array
     */
    protected static function getFileDisplayersBaseTemplates(ThemeInterface $theme): array
    {
        $templates = [];
        foreach (Themes::$plugin->layouts->getForTheme($theme) as $layout) {
            foreach ($layout->viewModes as $viewMode) {
                foreach ($viewMode->displays as $display) {
                    try {
                        $display->item;
                    } catch (\Throwable $e) {
                        continue;
                    }
                    if($display->type == DisplayService::TYPE_FIELD) {
                        foreach (static::getFileBaseTemplates($display, $layout) as $template) {
                            $templates[$template] = [
                                'source' => \Craft::t('themes', 'System')
                            ];
                        }
                    } else {
                        foreach ($display->item->displays as $groupDisplay) {
                            foreach (static::getFileBaseTemplates($groupDisplay, $layout) as $template) {
                                $templates[$template] = [
                                    'source' => \Craft::t('themes', 'System')
                                ];
                            }
                        }
                    }
                }
            }    
        }
        return $templates;
    }

    /**
     * Get all possible templates for a field displayer
     * 
     * @param  ThemeInterface $theme
     * @return array
     */
    protected static function getFieldBaseTemplates(DisplayInterface $display, LayoutInterface $layout): array
    {
        try {
            if (!$display->item->displayer) {
                return [];
            }
        } catch (\Throwable $e) {
            return [];
        }
        return $layout->getFieldTemplates($display->item);
    }

    /**
     * Get all possible templates for a file displayer
     * 
     * @param  ThemeInterface $theme
     * @return array
     */
    protected static function getFileBaseTemplates(DisplayInterface $display, LayoutInterface $layout): array
    {
        try {
            if (!$display->item->displayer instanceof FileFieldDisplayerInterface) {
                return [];
            }
        } catch (\Throwable $e) {
            return [];
        }
        $templates = [];
        foreach (Themes::$plugin->fileDisplayers->all as $handle => $class) {
            $fileDisplayer = Themes::$plugin->fileDisplayers->getByHandle($handle);
            $templates = array_merge($templates, $layout->getFileTemplates($display->item, $fileDisplayer));
        }
        return $templates;
    }
}