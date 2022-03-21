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
use Ryssbowh\CraftThemes\models\blocks\ContentBlock;
use Ryssbowh\CraftThemes\services\LayoutService;
use Twig\Markup;
use craft\base\Element;
use craft\elements\Asset;
use craft\events\TemplateEvent;
use craft\helpers\Template;
use yii\base\Event;
use yii\caching\TagDependency;

class ViewService extends Service
{
    const TEMPLATE_CACHE_TAG = 'themes::templates';

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
     * Rendering layout mode
     * @var ?string
     */
    protected $_renderingMode;

    /**
     * Rendering layout mode
     * @var ?string
     */
    protected $_renderingBlock;

    /**
     * Variables originally passed to the page
     * @var array
     */
    protected $pageVariables = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        if (getenv('ENVIRONMENT') == 'production') {
            $this->devMode = false;
        }
    }

    /**
     * Handle current page rendering.
     * If a theme is set then we'll look for the theme layout for that request and add it to the template variables.
     * 
     * @param TemplateEvent $event
     */
    public function beforeRenderPage(TemplateEvent $event)
    {
        if (!Themes::$plugin->is(Themes::EDITION_PRO)) {
            return;
        }
        if (!$theme = $this->themesRegistry()->current) {
            //No theme is defined for that request
            return;
        }
        if (!$element = \Craft::$app->urlManager->getMatchedElement()) {
            //No matched element for that request
            return;
        }
        if (!$layout = $this->layoutService()->resolveForRequest($theme, $element)) {
            //No layout has been found for that request
            return;
        }
        \Craft::info('Found layout "' . $layout->description . '" (id: ' . $layout->id . ')', __METHOD__);
        if ($this->eagerLoad) {
            $with = $this->eagerLoadingService()->getEagerLoadable($layout->defaultViewMode);
            \Craft::info('Eager loaded fields : ' . json_encode($with), __METHOD__);
            \Craft::$app->elements->eagerLoadElements(get_class($element), [$element], $with);
        }
        $this->_renderingElement = $element;
        $this->pageVariables = $event->variables;
        $event->variables = array_merge($event->variables, [
            'layout' => $layout,
            'element' => $element
        ]);
    }

    /**
     * Renders a region
     * 
     * @param  Region  $region
     * @return string
     */
    public function renderRegion(RegionInterface $region): Markup
    {
        if (!$region->beforeRender()) {
            return Template::raw('');
        }
        $oldRegion = $this->renderingRegion;
        $this->_renderingRegion = $region;
        $theme = $this->themesRegistry()->current;
        $templates = $region->getTemplates($this->renderingLayout);
        $variables = $this->getPageVariables([
            'classes' => new ClassBag($theme->preferences->getRegionClasses($region)),
            'attributes' => new AttributeBag($theme->preferences->getRegionAttributes($region)),
            'region' => $region,
        ]);
        $markup = $this->render(self::BEFORE_RENDERING_REGION, $templates, $variables);
        $this->_renderingRegion = $oldRegion;
        return $markup;
    }

    /**
     * Renders a block
     * 
     * @param  BlockInterface $block
     * @param  Element        $element
     * @return string
     */
    public function renderBlock(BlockInterface $block): Markup
    {
        if (!$block->beforeRender()) {
            return Template::raw('');
        }
        $oldBlock = $this->renderingBlock;
        $this->_renderingBlock = $block;
        $theme = $this->themesRegistry()->current;
        $templates = $block->getTemplates($this->renderingLayout);
        $variables = $this->getPageVariables([
            'classes' => new ClassBag($theme->preferences->getBlockClasses($block)),
            'attributes' => new AttributeBag($theme->preferences->getBlockAttributes($block)),
            'block' => $block,
        ]);
        $markup = $this->render(self::BEFORE_RENDERING_BLOCK, $templates, $variables);
        $this->_renderingBlock = $oldBlock;
        return $markup;
    }

    /**
     * Renders a field
     * 
     * @param  FieldInterface $field
     * @param  mixed          $value
     * @return string
     */
    public function renderField(FieldInterface $field, $value): Markup
    {
        if (!$displayer = $field->getDisplayer() or !$displayer->beforeRender($value)) {
            return Template::raw('');
        }
        $theme = $this->themesRegistry()->current;
        $templates = $field->getFieldTemplates();
        $variables = $this->getPageVariables([
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
            'label' => $field->name,
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
    public function renderGroup(GroupInterface $group): Markup
    {
        if (!$group->beforeRender()) {
            return Template::raw('');
        }
        $theme = $this->themesRegistry()->current;
        $templates = $group->getTemplates();
        $variables = $this->getPageVariables([
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
    public function renderFile(Asset $asset, FieldInterface $field, ?FileDisplayerInterface $displayer): Markup
    {
        if (!$displayer or !$displayer->beforeRender($asset)) {
            return Template::raw('');
        }
        $theme = $this->themesRegistry()->current;
        $templates = $field->getFileTemplates($displayer);
        $variables = $this->getPageVariables([
            'classes' => new ClassBag($theme->preferences->getFileClasses($asset, $field, $displayer)),
            'attributes' => new AttributeBag($theme->preferences->getFileAttributes($asset, $field, $displayer)),
            'asset' => $asset,
            'field' => $field,
            'displayer' => $displayer,
            'options' => $displayer->options
        ]);
        return $this->render(self::BEFORE_RENDERING_FILE, $templates, $variables);
    }

    /**
     * Renders a layout for a view mode and an element
     * 
     * @param  LayoutInterface          $layout
     * @param  string|ViewModeInterface $viewMode
     * @param  ?Element                 $element
     * @param  string                   $mode
     * @return string
     */
    public function renderLayout(LayoutInterface $layout, $viewMode, ?Element $element, string $mode = LayoutInterface::RENDER_MODE_DISPLAYS): Markup
    {
        if (is_string($viewMode)) {
            $viewMode = $layout->getViewMode($viewMode);
        }
        $theme = $this->themesRegistry()->current;
        $oldLayout = $this->renderingLayout;
        $oldViewMode = $this->renderingViewMode;
        $oldElement = $this->renderingElement;
        $oldMode = $this->renderingMode;

        $this->_renderingLayout = $layout;
        $this->_renderingViewMode = $viewMode;
        $this->_renderingMode = $mode;
        if ($element) {
            $this->_renderingElement = $element;
        }
        
        $variables = $this->getPageVariables([
            'classes' => new ClassBag($theme->preferences->getLayoutClasses($layout, true)),
            'attributes' => new AttributeBag($theme->preferences->getLayoutAttributes($layout, true)),
            'visibleDisplays' => $viewMode->visibleDisplays,
            'regions' => $layout->regions,
            'layout' => $layout,
            'viewMode' => $viewMode
        ]);
        if ($mode == LayoutInterface::RENDER_MODE_DISPLAYS) {
            $templates = $layout->getTemplates($viewMode);
        } else {
            $templates = [
                $theme->getRegionsTemplate()
            ];
        }
        $markup = $this->render(self::BEFORE_RENDERING_LAYOUT, $templates, $variables);
        $this->_renderingLayout = $oldLayout;
        $this->_renderingViewMode = $oldViewMode;
        $this->_renderingElement = $oldElement;
        $this->_renderingMode = $oldMode;
        return $markup;
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
     * Get the current rendering block
     * 
     * @return ?ViewModeInterface
     */
    public function getRenderingBlock(): ?BlockInterface
    {
        return $this->_renderingBlock;
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
     * Get the current rendering layout mode
     * 
     * @return ?string
     */
    public function getRenderingMode(): ?string
    {
        return $this->_renderingMode;
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
     * @param  string   $eventType
     * @param  string[] $templates
     * @param  array    $variables
     * @return string
     */
    protected function render(string $eventType, array $templates, array $variables): Markup
    {
        $event = $this->triggerRenderingEvent($eventType, $templates, $variables);
        if (!$event->render) {
            return '';
        }
        $template = $this->resolveTemplateFromCache($event->templates);   
        $html = $this->getDevModeHtml($event->templates, $template, $event->variables);
        $html .= \Craft::$app->view->renderTemplate($template, $event->variables);
        return Template::raw($html);
    }

    /**
     * Triggers an event
     * 
     * @param  string   $eventType
     * @param  string[] $templates
     * @param  array    $variables
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
     * @param  string[] $templates
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
     * @param  string[] $templates
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
     * @param  string[] $templates
     * @param  string   $currentTemplate
     * @param  array    $variables
     * @return string
     */
    protected function getDevModeHtml(array $templates, string $currentTemplate, array $variables): string
    {
        if (!$this->devMode) {
            return '';
        }
        $html = "<!-- ***** available templates : ***** -->";
        foreach ($templates as $template) {
            $html .= "<!-- " . $template . ($template == $currentTemplate ? " (current)" : "") . "-->";
        }
        $html .= "<!-- ***** available variables : ***** -->";
        foreach ($variables as $name => $variable) {
            $html .= "<!-- " . $name . ' (' . (gettype($variable) == 'object' ? get_class($variable) : gettype($variable)) . ")-->";
        }
        return $html;
    }

    /**
     * Add the page variables and the themes variables to an array
     * 
     * @param  array  $variables
     * @return array
     */
    protected function getPageVariables(array $variables = []): array
    {
        $variables = array_merge($this->pageVariables, $variables);
        $variables['element'] = $this->renderingElement;
        $variables['layout'] = $this->renderingLayout;
        $variables['region'] = $this->renderingRegion;
        $variables['viewMode'] = $this->renderingViewMode;
        return $variables;
    }
}