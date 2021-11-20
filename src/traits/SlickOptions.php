<?php
namespace Ryssbowh\CraftThemes\traits;

trait SlickOptions
{
    /**
     * @var string
     */
    public $lazyLoad = 'ondemand';

    /**
     * @var string
     */
    public $autoplaySpeed = 3000;
    
    /**
     * @var boolean
     */
    public $adaptiveHeight = false;

    /**
     * @var boolean
     */
    public $autoplay = false;

    /**
     * @var boolean
     */
    public $arrows = true;

    /**
     * @var boolean
     */
    public $dots = false;

    /**
     * @var boolean
     */
    public $draggable = true;

    /**
     * @var boolean
     */
    public $fade = false;

    /**
     * @var boolean
     */
    public $infinite = true;

    /**
     * @var boolean
     */
    public $pauseOnFocus = true;

    /**
     * @var boolean
     */
    public $pauseOnHover = true;

    /**
     * @var boolean
     */
    public $swipe = true;

    /**
     * @var boolean
     */
    public $touchMove = true;

    /**
     * @var boolean
     */
    public $vertical = false;

    /**
     * @var boolean
     */
    public $verticalSwiping = false;

    /**
     * @var boolean
     */
    public $rtl = false;

    /**
     * @var integer
     */
    public $rows = 1;

    /**
     * @var integer
     */
    public $slidesPerRow = 1;

    /**
     * @var integer
     */
    public $slidesToScroll = 1;

    /**
     * @var integer
     */
    public $slidesToShow = 1;

    /**
     * @var integer
     */
    public $speed = 300;

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