<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\events\RenderEvent;
use Ryssbowh\CraftThemes\helpers\AttributeBag;
use Ryssbowh\CraftThemes\helpers\ClassBag;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\FieldInterface;
use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use Ryssbowh\CraftThemes\interfaces\LayoutInterface;
use Ryssbowh\CraftThemes\models\Region;
use Ryssbowh\CraftThemes\models\fields\CraftField;
use Ryssbowh\CraftThemes\services\LayoutService;
use craft\base\Element;
use craft\elements\Asset;
use craft\events\TemplateEvent;
use yii\base\Event;
use yii\caching\TagDependency;

class ViewService extends Service
{
    const TEMPLATE_CACHE_TAG = 'themes.templates';

    const THEME_ROOT_TEMPLATE = 'themed_page';

    const BEFORE_RENDERING_LAYOUT = 'before_rendering_layout';
    const BEFORE_RENDERING_ASSET = 'before_rendering_asset';
    const BEFORE_RENDERING_FIELD = 'before_rendering_field';
    const BEFORE_RENDERING_BLOCK = 'before_rendering_block';
    const BEFORE_RENDERING_REGION = 'before_rendering_region';

    /**
     * View mode being rendered
     * @var ?string
     */
    protected $renderingViewMode = LayoutService::DEFAULT_HANDLE;

    /**
     * Layout being rendered
     * @var ?LayoutInterface
     */
    protected $renderingLayout;

    /**
     * Region being rendered
     * @var ?Region
     */
    protected $renderingRegion;

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
     * Handle current page rendering.
     * If a theme is set and the section of the element being rendered is set to 'themed_page'
     * then we'll look for the theme layout for that section and add it to the template variables.
     * 
     * @param  TemplateEvent $event
     */
    public function beforeRenderPage(TemplateEvent $event)
    {
        if (!$element = \Craft::$app->urlManager->getMatchedElement()) {
            //no elements have matched that request
            return;
        }
        if ($event->template != self::THEME_ROOT_TEMPLATE) {
            //This is not a theme driven template
            return;
        }
        if (!$theme = $this->themesRegistry()->getCurrent()) {
            //No theme is defined for that request
            return;
        }
        if (!$layout = Themes::$plugin->layouts->resolveForRequest($theme->handle, $element)) {
            //no theme layout is defined for that request
            return;
        }
        $this->renderingLayout = $layout;
        $event->variables['element'] = $element;
        $event->variables['layout'] = $layout;
    }

    /**
     * Renders a region
     * 
     * @param  Region  $region
     * @param  Element $element
     * @return string
     */
    public function renderRegion(Region $region, Element $element): string
    {
        $this->renderingRegion = $region;
        $layout = $this->renderingLayout->getElementMachineName();
        $type = $this->renderingLayout->type;
        $templates = [
            'regions/' . $type . '/' . $layout . '/region' . $region->handle,
            'regions/' . $type . '/' . $layout . '/region',
            'regions/' . $type . '/region-' . $region->handle,
            'regions/' . $type . '/region',
            'regions/region-' . $region->handle, 
            'regions/region'
        ];
        $variables = [
            'classes' => new ClassBag(['region', $region->handle]),
            'attributes' => new AttributeBag(['id' => $region->handle]),
            'region' => $region,
            'layout' => $this->renderingLayout,
            'viewMode' => $this->renderingViewMode,
            'element' => $element,
        ];
        $event = new RenderEvent([
            'templates' => $templates,
            'variables' => $variables
        ]);
        return $this->render(self::BEFORE_RENDERING_REGION, $event);
    }

    /**
     * Renders a block
     * 
     * @param  BlockInterface $block
     * @param  Element        $element
     * @return string
     */
    public function renderBlock(BlockInterface $block, Element $element): string
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
        $templates = [
            'blocks/' . $type . '/' . $layout . '/' . $region . '/' . $machineName,
            'blocks/' . $type . '/' . $layout . '/' . $machineName,
            'blocks/' . $type . '/' . $machineName,
            'blocks/' . $machineName, 
            'blocks/block'
        ];
        $variables = [
            'classes' => new ClassBag(['block', $block->getMachineName()]),
            'attributes' => new AttributeBag,
            'block' => $block,
            'layout' => $this->renderingLayout,
            'viewMode' => $this->renderingViewMode,
            'element' => $element,
        ];
        $event = new RenderEvent([
            'templates' => $templates,
            'variables' => $variables
        ]);
        $data = $this->render(self::BEFORE_RENDERING_BLOCK, $event);
        $this->blockCacheService()->stopBlockCaching($block, $data);
        return $data;
    }

    /**
     * Renders a field
     * 
     * @param  FieldInterface $field
     * @param  Element        $element
     * @param  mixed          $value
     * @return string
     */
    public function renderField(FieldInterface $field, Element $element, $value): string
    {
        if (!$displayer = $field->getDisplayer()) {
            return '';
        }
        $layout = $this->renderingLayout->getElementMachineName();
        $type = $this->renderingLayout->type;
        $viewMode = $this->renderingViewMode;
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
        $variables = [
            'classes' => new ClassBag(['field', 'field-' . $field->handle, $displayer->handle]),
            'attributes' => new AttributeBag,
            'field' => $field,
            'display' => $field->display,
            'layout' => $this->renderingLayout,
            'viewMode' => $this->renderingViewMode,
            'element' => $element,
            'displayer' => $displayer,
            'options' => $displayer->getOptions(),
            'value' => $value,
            'craftField' => ($field instanceof CraftField ? $field->craftField : null)
        ];
        $event = new RenderEvent([
            'templates' => $templates,
            'variables' => $variables
        ]);
        return $this->render(self::BEFORE_RENDERING_FIELD, $event);
    }

    /**
     * Renders an asset
     * 
     * @param  Asset                  $asset
     * @param  FileDisplayerInterface $displayer
     * @return string
     */
    public function renderAsset(Asset $asset, ?FileDisplayerInterface $displayer): string
    {
        if (!$displayer) {
            return '';
        }
        $layout = $this->renderingLayout->getElementMachineName();
        $type = $this->renderingLayout->type;
        $viewMode = $this->renderingViewMode;
        $handle = $displayer->handle;
        $templates = [
            'assets/' . $type . '/' . $layout . '/' . $viewMode . '/' . $handle,
            'assets/' . $type . '/' . $layout . '/' . $handle,
            'assets/' . $type . '/' . $handle,
            'assets/' . $handle
        ];
        $variables = [
            'asset' => $asset,
            'displayer' => $displayer,
            'options' => $displayer->options
        ];
        $event = new RenderEvent([
            'templates' => $templates,
            'variables' => $variables
        ]);
        return $this->render(self::BEFORE_RENDERING_ASSET, $event);
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
        $oldLayout = $this->renderingLayout;
        $oldViewMode = $this->renderingViewMode;
        $this->renderingLayout = $layout;
        $this->renderingViewMode = $viewMode;
        $machineName = $layout->getElementMachineName();
        $type = $layout->type;
        $templates = [
            'layouts/' . $type . '/' . $machineName . '/' . $viewMode,
            'layouts/' . $type . '/' . $machineName,
            'layouts/' . $type,
            'layouts/layout'
        ];
        $variables = [
            'classes' => new ClassBag(['layout', 'layout-type-' . $layout->type, 'view-mode-'.$viewMode, 'layout-handle-' . $machineName]),
            'attributes' => new AttributeBag,
            'layout' => $layout,
            'viewMode' => $viewMode,
            'mode' => $layout->getRenderingMode(),
            'element' => $element
        ];
        $event = new RenderEvent([
            'templates' => $templates,
            'variables' => $variables,
        ]);
        $html = $this->render(self::BEFORE_RENDERING_LAYOUT, $event);
        $this->renderingLayout = $oldLayout;
        $this->renderingViewMode = $oldViewMode;
        return $html;
    }

    /**
     * Get the current rendering view mode
     * 
     * @return ?string
     */
    public function getRenderingViewMode(): ?string
    {
        return $this->renderingViewMode;
    }

    /**
     * Get the current rendering layout
     * 
     * @return LayoutInterface
     */
    public function getRenderingLayout(): ?LayoutInterface
    {
        return $this->renderingLayout;
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
     * @param  Event $event
     * @return string
     */
    protected function render(string $eventType, Event $event): string
    {
        $this->triggerEvent($eventType, $event);
        $template = $this->resolveTemplateFromCache($event->templates);   
        $html = $this->getDevModeHtml($event->templates, $template, $event->variables);
        $html .= \Craft::$app->view->renderTemplate($template, $event->variables);
        return $html;
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
            $key = \Craft::$app->cache->buildKey($templates);
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
}