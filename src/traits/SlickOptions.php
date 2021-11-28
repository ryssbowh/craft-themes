<?php
namespace Ryssbowh\CraftThemes\traits;

/**
 * Trait to be used for configurable options that handle a slick carousel
 */
trait SlickOptions
{
    /**
     * @inheritDoc
     */
    public function defineSlickOptions(): array
    {
        return [
            'lazyLoad' => [
                'field' => 'select',
                'options' => [
                    'ondemand' => \Craft::t('themes', 'On demand'),
                    'progressive' => \Craft::t('themes', 'Progressive')
                ],
                'label' => \Craft::t('themes', 'Lazy load')
            ],
            'autoplay' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Autoplay')
            ],
            'autoplaySpeed' => [
                'field' => 'text',
                'type' => 'number',
                'min' => 100,
                'step' => 100,
                'required' => true,
                'label' => \Craft::t('themes', 'Autoplay speed')
            ],
            'adaptiveHeight' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Adaptive height')
            ],
            'arrows' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Show arrows')
            ],
            'dots' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Show dots')
            ],
            'draggable' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Draggable')
            ],
            'fade' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Fade')
            ],
            'infinite' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Infinite')
            ],
            'pauseOnFocus' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Pause on focus')
            ],
            'pauseOnHover' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Pause on hover')
            ],
            'swipe' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Enable swipe')
            ],
            'touchMove' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Enable touch move')
            ],
            'vertical' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Vertical')
            ],
            'verticalSwiping' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Vertical swiping')
            ],
            'rtl' => [
                'field' => 'lightswitch',
                'label' => \Craft::t('themes', 'Right to left')
            ],
            'rows' => [
                'field' => 'text',
                'type' => 'number',
                'min' => 1,
                'step' => 1,
                'required' => true,
                'label' => \Craft::t('themes', 'Rows')
            ],
            'slidesToShow' => [
                'field' => 'text',
                'type' => 'number',
                'min' => 1,
                'step' => 1,
                'required' => true,
                'label' => \Craft::t('themes', 'Slides to show')
            ],
            'slidesPerRow' => [
                'field' => 'text',
                'type' => 'number',
                'min' => 1,
                'step' => 1,
                'required' => true,
                'label' => \Craft::t('themes', 'Slides per row')
            ],
            'slidesToScroll' => [
                'field' => 'text',
                'type' => 'number',
                'min' => 1,
                'step' => 1,
                'required' => true,
                'label' => \Craft::t('themes', 'Slides to scroll')
            ],
            'speed' => [
                'field' => 'text',
                'type' => 'number',
                'min' => 100,
                'step' => 100,
                'required' => true,
                'label' => \Craft::t('themes', 'Animation speed')
            ],
        ];
    }

    public function defineDefaultSlickValues(): array
    {
        return [
            'lazyLoad' => 'ondemand',
            'autoplaySpeed' => 3000,
            'autoplay' => false,
            'adaptiveHeight' => false,
            'arrows' => true,
            'dots' => false,
            'draggable' => false,
            'fade' => false,
            'infinite' => true,
            'pauseOnFocus' => true,
            'pauseOnHover' => true,
            'swipe' => true,
            'touchMove' => true,
            'vertical' => false,
            'verticalSwiping' => false,
            'rtl' => false,
            'rows' => 1,
            'slidesPerRow' => 1,
            'slidesToScroll' => 1,
            'slidesToShow' => 1,
            'speed' => 300,
        ];
    }

    /**
     * @inheritDoc
     */
    public function defineSlickRules(): array
    {
        return [
            [['autoplaySpeed', 'rows', 'slidesPerRow', 'slidesToScroll', 'speed', 'slidesToShow'], 'integer'],
            [['autoplaySpeed', 'rows', 'slidesPerRow', 'slidesToScroll', 'speed', 'slidesToShow'], 'required'],
            ['lazyLoad', 'in', 'range' => ['ondemand', 'progressive']],
            [['adaptiveHeight', 'autoplay', 'arrows', 'dots', 'draggable', 'fade', 'infinite', 'pauseOnFocus', 'pauseOnHover', 'swipe', 'touchMove', 'vertical', 'verticalSwiping', 'rtl'], 'boolean', 'trueValue' => true, 'falseValue' => false],
        ];
    }
}