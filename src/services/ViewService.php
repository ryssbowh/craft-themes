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

class ViewService extends Service
{
    const CACHE_GROUP = 'themes.templates.';

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
    public $devMode = false;

    /**
     * @var boolean
     */
    public $eagerLoad;

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
            'regions/' . $type . '/' . $layout . '/' . $region->handle,
            'regions/' . $type . '/' . $layout . '/region',
            'regions/' . $type . '/' . $region->handle,
            'regions/' . $type . '/region',
            'regions/' . $region->handle, 
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
        return $this->render(self::BEFORE_RENDERING_BLOCK, $event);
    }

    /**
     * Renders a field
     * 
     * @param  FieldInterface $field
     * @param  Element        $element
     * @return string
     */
    public function renderField(FieldInterface $field, Element $element): string
    {
        if (!$displayer = $field->getDisplayer()) {
            return '';
        }
        $layout = $this->renderingLayout->getElementMachineName();
        $type = $this->renderingLayout->type;
        $viewMode = $this->renderingViewMode;
        $handle = $displayer->handle;
        // $template = $this->cacheService()->get('fields/' . $type . '/' . $layout . '/' . $viewMode . '/' . $handle);
        $templates = [
            'fields/' . $type . '/' . $layout . '/' . $viewMode . '/' . $handle,
            'fields/' . $type . '/' . $layout . '/' . $handle,
            'fields/' . $type . '/' . $handle,
            'fields/' . $handle
        ];
        $variables = [
            'classes' => new ClassBag(['field', $displayer->handle]),
            'attributes' => new AttributeBag,
            'field' => $field,
            'display' => $field->display,
            'layout' => $this->renderingLayout,
            'viewMode' => $this->renderingViewMode,
            'element' => $element,
            'displayer' => $displayer,
            'options' => $displayer->getOptions(),
            'value' => $element->{$field->handle},
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
     * Renders an array of templates
     *
     * @param  string $eventType
     * @param  Event $event
     * @return string
     */
    protected function render(string $eventType, Event $event): string
    {
        $this->triggerEvent($eventType, $event);
        $template = $this->resolveTemplate($event->templates);   
        $html = $this->getDevModeHtml($event->templates, $template, $event->variables);
        $html .= \Craft::$app->view->renderTemplate($template, $event->variables);
        return $html;
    }

    /**
     * Resolves a template from an array of templates
     * 
     * @param  array  $templates
     * @return string
     */
    protected function resolveTemplate(array $templates): string
    {
        $key = md5(implode('-', $templates));
        $template = $this->cacheService()->get(self::CACHE_GROUP, $key);
        // if ($template === false) {
            $twig = \Craft::$app->view->getTwig();
            $template = $twig->resolveTemplate($templates)->getTemplateName();
            // $this->cacheService()->set($key, $template);
        // }
        return $template;
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