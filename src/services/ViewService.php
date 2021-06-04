<?php 

namespace Ryssbowh\CraftThemes\services;

use Ryssbowh\CraftThemes\Themes;
use Ryssbowh\CraftThemes\helpers\AttributeBag;
use Ryssbowh\CraftThemes\helpers\ClassBag;
use Ryssbowh\CraftThemes\interfaces\BlockInterface;
use Ryssbowh\CraftThemes\interfaces\FileDisplayerInterface;
use Ryssbowh\CraftThemes\models\Display;
use Ryssbowh\CraftThemes\models\Region;
use Ryssbowh\CraftThemes\models\fields\CraftField;
use Ryssbowh\CraftThemes\models\fields\Field;
use Ryssbowh\CraftThemes\models\layouts\Layout;
use Ryssbowh\CraftThemes\services\LayoutService;
use Ryssbowh\CraftThemes\services\ViewModeService;
use craft\base\Element;
use craft\elements\Asset;
use craft\events\TemplateEvent;

class ViewService extends Service
{
    const CACHE_GROUP = 'themes.templates.';

    const THEME_ROOT_TEMPLATE = 'themed_page';

    /**
     * View mode being rendered
     * @var ?string
     */
    protected $renderingViewMode;

    /**
     * Layout being rendered
     * @var ?Layout
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
        $event->variables['element'] = $element;
        $event->variables['layout'] = $layout;
    }

    public function renderRegion(Region $region, Element $element): string
    {
        $this->renderingRegion = $region;
        $layout = $this->renderingLayout->getElementMachineName();
        $type = $this->renderingLayout->type;
        return $this->render(
            [
                'regions/' . $type . '/' . $layout . '/' . $region->handle,
                'regions/' . $type . '/' . $layout . '/region',
                'regions/' . $type . '/' . $region->handle,
                'regions/' . $type . '/region',
                'regions/' . $region->handle, 
                'regions/region'
            ],
            [
                'classes' => new ClassBag(['region', $region->handle]),
                'attributes' => new AttributeBag(['id' => $region->handle]),
                'region' => $region,
                'layout' => $this->renderingLayout,
                'viewMode' => $this->renderingViewMode,
                'element' => $element,
            ]
        );
    }

    public function renderBlock(BlockInterface $block, Element $element): string
    {
        $region = $this->renderingRegion->handle;
        $layout = $this->renderingLayout->getElementMachineName();
        $machineName = $block->getMachineName();
        $type = $this->renderingLayout->type;
        return $this->render(
            [
                'blocks/' . $type . '/' . $layout . '/' . $region . '/' . $machineName,
                'blocks/' . $type . '/' . $layout . '/' . $machineName,
                'blocks/' . $type . '/' . $machineName,
                'blocks/' . $machineName, 
                'blocks/block'
            ],
            [
                'classes' => new ClassBag(['block', $block->getMachineName()]),
                'attributes' => new AttributeBag,
                'block' => $block,
                'layout' => $this->renderingLayout,
                'viewMode' => $this->renderingViewMode,
                'element' => $element,
            ]
        );
    }

    public function renderField(Field $field, Element $element)
    {
        if (!$displayer = $field->getDisplayer()) {
            return '';
        }
        $layout = $this->renderingLayout->getElementMachineName();
        $type = $this->renderingLayout->type;
        $viewMode = $this->renderingViewMode;
        $handle = $displayer->handle;
        // $template = $this->cacheService()->get('fields/' . $type . '/' . $layout . '/' . $viewMode . '/' . $handle);
        return $this->render(
            [
                'fields/' . $type . '/' . $layout . '/' . $viewMode . '/' . $handle,
                'fields/' . $type . '/' . $layout . '/' . $handle,
                'fields/' . $type . '/' . $handle,
                'fields/' . $handle
            ],
            [
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
            ]
        );
    }

    public function renderAsset(Asset $asset, ?FileDisplayerInterface $displayer)
    {
        if (!$displayer) {
            return '';
        }
        $layout = $this->renderingLayout->getElementMachineName();
        $type = $this->renderingLayout->type;
        $viewMode = $this->renderingViewMode;
        $handle = $displayer->handle;
        return $this->render(
            [
                'assets/' . $type . '/' . $layout . '/' . $viewMode . '/' . $handle,
                'assets/' . $type . '/' . $layout . '/' . $handle,
                'assets/' . $type . '/' . $handle,
                'assets/' . $handle
            ],
            [
                'asset' => $asset,
                'displayer' => $displayer,
                'options' => $displayer->options
            ]
        );
    }

    public function renderLayout(Layout $layout, string $viewMode, Element $element)
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
        $html = $this->render(
            [
                'layouts/' . $type . '/' . $machineName . '/' . $viewMode, 
                'layouts/' . $type . '/' . $machineName, 
                'layouts/' . $type, 
                'layouts/layout'
            ],
            [
                'classes' => new ClassBag(['layout', 'layout-type-' . $layout->type, 'view-mode-'.$viewMode, 'layout-handle-' . $machineName]),
                'attributes' => new AttributeBag,
                'layout' => $layout,
                'regions' => $layout->regions,
                'viewMode' => $viewMode,
                'mode' => $layout->getRenderingMode(),
                'element' => $element
            ]
        );
        $this->renderingLayout = $oldLayout;
        $this->renderingViewMode = $oldViewMode;
        return $html;
    }

    public function getRenderingViewMode(): ?string
    {
        return $this->renderingViewMode;
    }

    public function getRenderingLayout(): ?Layout
    {
        return $this->renderingLayout;
    }

    protected function render(array $templates, array $variables)
    {
        $template = $this->resolveTemplate($templates);   
        $html = $this->getDevModeHtml($templates, $template, $variables);
        $html .= \Craft::$app->view->renderTemplate($template, $variables);
        return $html;
    }

    protected function resolveTemplate(array $templates)
    {
        $key = $templates[0];
        $template = $this->cacheService()->get(self::CACHE_GROUP, $key);
        // if ($template === false) {
            $twig = \Craft::$app->view->getTwig();
            $template = $twig->resolveTemplate($templates)->getTemplateName();
            // $this->cacheService()->set($key, $template);
        // }
        return $template;
    }

    protected function getDevModeHtml(array $templates, string $current, array $variables)
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