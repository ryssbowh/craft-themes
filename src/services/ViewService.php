<?php
namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\RenderEvent;
use Ryssbowh\CraftThemes\helpers\AttributeBag;
use Ryssbowh\CraftThemes\helpers\ClassBag;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\CraftFieldInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\GroupInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\interfaces\RegionInterface;
use Ryssbowh\CraftThemes\interfaces\ViewModeInterface;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\base\Element;
use craft\elements\Asset;
use craft\events\TemplateEvent;
use yii\base\Event;
use yii\caching\TagDependency;

class ViewService extends Service
{
    const TEMPLATE_CACHE_TAG = 'themes.templates';

    const BEFORE_RENDERING_LAYOUT = 'before_rendering_layout';
    const BEFORE_RENDERING_FILE = 'before_rendering_file';
    const BEFORE_RENDERING_FIELD = 'before_rendering_field';
    const BEFORE_RENDERING_BLOCK = 'before_rendering_block';
    const BEFORE_RENDERING_REGION = 'before_rendering_region';
    const BEFORE_RENDERING_GROUP = 'before_rendering_group';

    /**
     * @var boolean
     */
    public $devMode;

    /**
     * @var boolean
     */
    public $templateCacheEnabled;

    /**
     * @var boolean
     */
    public $eagerLoad;

    /**
     * @var CacheInterface
     */
    public $cache;

    /**
     * View mode being rendered
     * @var ?ViewModeInterface
     */
    protected $_renderingViewMode;

    /**
     * Layout being rendered
     * @var ?LayoutInterface
     */
    protected $_renderingLayout;

    /**
     * Region being rendered
     * @var ?Region
     */
    protected $_renderingRegion;

    /**
     * Element being rendered
     * @var ?Element
     */
    protected $_renderingElement;

    /**
     * Handle current page rendering.
     * If a theme is set and the section of the element being rendered is set to 'themed_page'
     * then we'll look for the theme layout for that section and add it to the template variables.
     * 
     * @param TemplateEvent $event
     */
    public function beforeRenderPage(TemplateEvent $event)
    {
        if (!$theme = $this->themesRegistry()->current) {
            //No theme is defined for that request
            return;
        }
        $element = \Craft::$app->urlManager->getMatchedElement();
        $layout = Themes::$plugin->layouts->resolveForRequest($theme, $element);
        $viewMode = $layout->getViewMode('default');
        if ($this->eagerLoad and $element) {
            $layout->eagerLoadFields($element, $viewMode->handle);
        }
        $this->_renderingLayout = $layout;
        $this->_renderingViewMode = $viewMode;
        $this->_renderingElement = $element;
        $variables = $this->getTemplateVariables([
            'classes' => new ClassBag($theme->preferences->getLayoutClasses($layout)),
            'attributes' => new AttributeBag($theme->preferences->getLayoutAttributes($layout)),
            'visibleDisplays' => $viewMode->visibleDisplays
        ]);
        $event2 = $this->triggerRenderingEvent(self::BEFORE_RENDERING_LAYOUT, [], $variables);
        $event->variables = array_merge($event->variables, $event2->variables);
    }

    /**
     * Renders a region
     * 
     * @param  Region  $region
     * @return string
     */
    public function renderRegion(RegionInterface $region): string
    {
        $oldRegion = $this->renderingRegion;
        $this->_renderingRegion = $region;
        $layout = $this->renderingLayout->getElementMachineName();
        $type = $this->renderingLayout->type;
        $theme = $this->themesRegistry()->current;
        $templates = [
            'regions/' . $type . '/' . $layout . '/region-' . $region->handle,
            'regions/' . $type . '/' . $layout . '/region',
            'regions/' . $type . '/region-' . $region->handle,
            'regions/' . $type . '/region',
            'regions/region-' . $region->handle, 
            'regions/region'
        ];
        $variables = $this->getTemplateVariables([
            'classes' => new ClassBag($theme->preferences->getRegionClasses($region)),
            'attributes' => new AttributeBag($theme->preferences->getRegionAttributes($region)),
            'region' => $region,
        ]);
        $html = $this->render(self::BEFORE_RENDERING_REGION, $templates, $variables);
        $this->_renderingRegion = $oldRegion;
        return $html;
    }

    /**
     * Renders a block
     * 
     * @param  BlockInterface $block
     * @param  Element        $element
     * @return string
     */
    public function renderBlock(BlockInterface $block): string
    {
        $cache = $this->blockCacheService()->getBlockCache($block);
        if ($cache !== null) {
            return $cache;
        }
        $this->blockCacheService()->startBlockCaching($block);
        $region = $this->renderingRegion->handle;
        $layout = $this->renderingLayout->getElementMachineName();
        $machineName = $block->getMachineName();
        $type = $this->renderingLayout->type;
        $theme = $this->themesRegistry()->current;
        $templates = [
            'blocks/' . $type . '/' . $layout . '/' . $region . '/' . $machineName,
            'blocks/' . $type . '/' . $layout . '/' . $machineName,
            'blocks/' . $type . '/' . $machineName,
            'blocks/' . $machineName, 
            'blocks/block'
        ];
        $variables = $this->getTemplateVariables([
            'classes' => new ClassBag($theme->preferences->getBlockClasses($block)),
            'attributes' => new ClassBag($theme->preferences->getBlockAttributes($block)),
            'attributes' => new AttributeBag,
            'block' => $block,
        ]);
        $data = $this->render(self::BEFORE_RENDERING_BLOCK, $templates, $variables);
        $this->blockCacheService()->stopBlockCaching($block, $data);
        return $data;
    }

    /**
     * Renders a field
     * 
     * @param  FieldInterface $field
     * @param  mixed          $value
     * @return string
     */
    public function renderField(FieldInterface $field, $value): string
    {
        if (!$displayer = $field->getDisplayer()) {
            return '';
        }
        $layout = $this->renderingLayout->getElementMachineName();
        $theme = $this->themesRegistry()->current;
        $type = $this->renderingLayout->type;
        $viewMode = $this->renderingViewMode->handle;
        $handle = $displayer->handle;
        $withField = $handle . '-' . $field->handle;
        $templates = [
            'fields/' . $type . '/' . $layout . '/' . $viewMode . '/' . $withField,
            'fields/' . $type . '/' . $layout . '/' . $viewMode . '/' . $handle,
            'fields/' . $type . '/' . $layout . '/' . $withField,
            'fields/' . $type . '/' . $layout . '/' . $handle,
            'fields/' . $type . '/' . $withField,
            'fields/' . $type . '/' . $handle,
            'fields/' . $withField,
            'fields/' . $handle
        ];
        $variables = $this->getTemplateVariables([
            'classes' => new ClassBag($theme->preferences->getFieldClasses($field)),
            'attributes' => new AttributeBag($theme->preferences->getFieldAttributes($field)),
            'containerClasses' => new ClassBag($theme->preferences->getFieldContainerClasses($field)),
            'containerAttributes' => new AttributeBag($theme->preferences->getFieldContainerAttributes($field)),
            'labelClasses' => new ClassBag($theme->preferences->getFieldLabelClasses($field)),
            'labelAttributes' => new AttributeBag($theme->preferences->getFieldLabelAttributes($field)),
            'field' => $field,
            'displayer' => $displayer,
            'options' => $displayer->getOptions(),
            'value' => $value,
            'craftField' => ($field instanceof CraftFieldInterface ? $field->craftField : null)
        ]);
        return $this->render(self::BEFORE_RENDERING_FIELD, $templates, $variables);
    }

    /**
     * Renders a group
     * 
     * @param  GroupInterface $group
     * @param  Element        $element
     * @return string
     */
    public function renderGroup(GroupInterface $group): string
    {
        $layout = $this->renderingLayout->getElementMachineName();
        $type = $this->renderingLayout->type;
        $theme = $this->themesRegistry()->current;
        $viewMode = $this->renderingViewMode;
        $handle = $group->handle;
        $templates = [
            'groups/' . $type . '/' . $layout . '/' . $viewMode->handle . '/group-' . $handle,
            'groups/' . $type . '/' . $layout . '/' . $viewMode->handle . '/group',
            'groups/' . $type . '/' . $layout . '/group-' . $handle,
            'groups/' . $type . '/' . $layout . '/group',
            'groups/' . $type . '/group-' . $handle,
            'groups/' . $type . '/group',
            'groups/group-' . $handle,
            'groups/group'
        ];
        $variables = $this->getTemplateVariables([
            'classes' => new ClassBag($theme->preferences->getGroupClasses($group)),
            'attributes' => new AttributeBag($theme->preferences->getGroupAttributes($group)),
            'containerClasses' => new ClassBag($theme->preferences->getGroupContainerClasses($group)),
            'containerAttributes' => new AttributeBag($theme->preferences->getGroupContainerAttributes($group)),
            'labelClasses' => new ClassBag($theme->preferences->getGroupLabelClasses($group)),
            'labelAttributes' => new AttributeBag($theme->preferences->getGroupLabelAttributes($group)),
            'group' => $group,
            'visibleDisplays' => $group->visibleDisplays
        ]);
        return $this->render(self::BEFORE_RENDERING_GROUP, $templates, $variables);
    }

    /**
     * Renders an asset file
     * 
     * @param  Asset                  $asset
     * @param  FileDisplayerInterface $displayer
     * @return string
     */
    public function renderFile(Asset $asset, FieldInterface $field, ?FileDisplayerInterface $displayer): string
    {
        if (!$displayer) {
            return '';
        }
        $theme = $this->themesRegistry()->current;
        $layout = $this->renderingLayout->getElementMachineName();
        $type = $this->renderingLayout->type;
        $viewMode = $this->renderingViewMode;
        $handle = $displayer->handle;
        $withField = $handle . '-' . $field->handle;
        $templates = [
            'files/' . $type . '/' . $layout . '/' . $viewMode->handle . '/' . $withField,
            'files/' . $type . '/' . $layout . '/' . $viewMode->handle . '/' . $handle,
            'files/' . $type . '/' . $layout . '/' . $withField,
            'files/' . $type . '/' . $layout . '/' . $handle,
            'files/' . $type . '/' . $withField,
            'files/' . $type . '/' . $handle,
            'files/' . $withField,
            'files/' . $handle
        ];
        $variables = $this->getTemplateVariables([
            'classes' => new ClassBag($theme->preferences->getFileClasses($asset, $field, $displayer)),
            'attributes' => new AttributeBag($theme->preferences->getFileAttributes($asset, $field, $displayer)),
            'asset' => $asset,
            'displayer' => $displayer,
            'options' => $displayer->options
        ]);
        return $this->render(self::BEFORE_RENDERING_FILE, $templates, $variables);
    }

    /**
     * Renders a layout for a view mode and an element
     * 
     * @param  LayoutInterface $layout
     * @param  string          $viewMode
     * @param  Element         $element
     * @return string
     */
    public function renderLayout(LayoutInterface $layout, string $viewMode, Element $element): string
    {
        if ($this->eagerLoad) {
            $layout->eagerLoadFields($element, $viewMode);
        }
        $theme = $this->themesRegistry()->current;
        $viewMode = $layout->getViewMode($viewMode);
        $oldLayout = $this->renderingLayout;
        $oldViewMode = $this->renderingViewMode;
        $oldElement = $this->renderingElement;
        $this->_renderingLayout = $layout;
        $this->_renderingViewMode = $viewMode;
        $this->_renderingElement = $element;
        $machineName = $layout->getElementMachineName();
        $type = $layout->type;
        $templates = [
            'layouts/' . $type . '/' . $machineName . '/' . $viewMode->handle,
            'layouts/' . $type . '/' . $machineName,
            'layouts/' . $type . '/layout',
            'layouts/' . $type,
            'layouts/layout'
        ];
        $variables = $this->getTemplateVariables([
            'classes' => new ClassBag($theme->preferences->getLayoutClasses($layout, true)),
            'attributes' => new AttributeBag($theme->preferences->getLayoutAttributes($layout, true)),
            'visibleDisplays' => $viewMode->visibleDisplays
        ]);
        $html = $this->render(self::BEFORE_RENDERING_LAYOUT, $templates, $variables);
        $this->_renderingLayout = $oldLayout;
        $this->_renderingViewMode = $oldViewMode;
        $this->_renderingElement = $oldElement;
        return $html;
    }

    /**
     * Get the current rendering view mode
     * 
     * @return ?ViewModeInterface
     */
    public function getRenderingViewMode(): ?ViewModeInterface
    {
        return $this->_renderingViewMode;
    }

    /**
     * Get the current rendering layout
     * 
     * @return ?LayoutInterface
     */
    public function getRenderingLayout(): ?LayoutInterface
    {
        return $this->_renderingLayout;
    }

    /**
     * Get the current rendering region
     * 
     * @return ?RegionInterface
     */
    public function getRenderingRegion(): ?RegionInterface
    {
        return $this->_renderingRegion;
    }

    /**
     * Get the current rendering element
     * 
     * @return ?Element
     */
    public function getRenderingElement(): ?Element
    {
        return $this->_renderingElement;
    }

    /**
     * Flush template cache
     */
    public function flushTemplateCache()
    {
        TagDependency::invalidate($this->cache, self::TEMPLATE_CACHE_TAG);
    }

    /**
     * Renders an array of templates
     *
     * @param  string $eventType
     * @param  array  $templates
     * @param  array  $variables
     * @return string
     */
    protected function render(string $eventType, array $templates, array $variables): string
    {
        $event = $this->triggerRenderingEvent($eventType, $templates, $variables);
        $template = $this->resolveTemplateFromCache($event->templates);   
        $html = $this->getDevModeHtml($event->templates, $template, $event->variables);
        $html .= \Craft::$app->view->renderTemplate($template, $event->variables);
        return $html;
    }

    /**
     * Triggers an event
     * 
     * @param  string $eventType
     * @param  array  $templates
     * @param  array  $variables
     * @return Event
     */
    protected function triggerRenderingEvent(string $eventType, array $templates, array $variables): Event
    {
        $event = new RenderEvent([
            'templates' => $templates,
            'variables' => $variables
        ]);
        $this->triggerEvent($eventType, $event);
        return $event;
    }

    /**
     * Resolves a template from cache
     * 
     * @param  array  $templates
     * @return string
     */
    protected function resolveTemplateFromCache(array $templates): string
    {
        if ($this->templateCacheEnabled) {
            $key = $this->cache->buildKey($templates);
            $template = $this->cache->get($key);
            if ($template !== false) {
                return $template;
            }
            $dep = new TagDependency([
                'tags' => [self::TEMPLATE_CACHE_TAG]
            ]);
            $template = $this->resolveTemplate($templates);
            $this->cache->set($key, $template, null, $dep);
            return $template;
        }
        return $this->resolveTemplate($templates);
    }

    /**
     * Resolves a template from an array of templates
     * 
     * @param  array $templates
     * @return string
     */
    protected function resolveTemplate(array $templates): string
    {
        $twig = \Craft::$app->view->getTwig();
        return $twig->resolveTemplate($templates)->getTemplateName();
    }

    /**
     * Get the dev mode html
     * 
     * @param  array  $templates
     * @param  string $current
     * @param  array  $variables
     * @return string
     */
    protected function getDevModeHtml(array $templates, string $current, array $variables): string
    {
        if (!$this->devMode) {
            return '';
        }
        $html = "<!-- *** available templates : *** -->";
        foreach ($templates as $template) {
            $html .= "<!-- " . $template . ($template == $current ? " (current)" : "") . "-->";
        }
        $html .= "<!-- *** available variables : *** -->";
        foreach ($variables as $name => $variable) {
            $html .= "<!-- " . $name . ' (' . (gettype($variable) == 'object' ? get_class($variable) : gettype($variable)) . ")-->";
        }
        return $html;
    }

    /**
     * Add default variables to an array
     * 
     * @param  array  $variables
     * @return array
     */
    protected function getTemplateVariables(array $variables = []): array
    {
        $variables['element'] = $this->renderingElement;
        $variables['layout'] = $this->renderingLayout;
        $variables['region'] = $this->renderingRegion;
        $variables['viewMode'] = $this->renderingViewMode;
        return $variables;
    }
}