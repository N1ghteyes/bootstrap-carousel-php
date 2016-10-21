<?php

namespace n1ghteyes\bootstrapCarousel;
//@todo replace this with auto loading.
include 'slides/image.php';
use n1ghteyes\bootstrapCarousel\carousel\image;

  define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);
  /**
   * PHP class to handle caching for the Bootstrap Carousel library
   *
   * @author N1ghteyes - www.source-control.co.uk
   * @copyright 2016 N1ghteyes
   * @license license.txt The MIT License (MIT)
   * @link https://github.com/N1ghteyes/bootstrap-carousel-php
   *
   * @todo add video and html slide support
   */
  class carousel{

    public $id; //String | html ID of the carousel
    public $slideOnOpen = 'data-ride="carousel"'; //String | almost always set to data-ride="carousel". If you don't want the slider to auto slide, set this to a blank string
    public $nav = TRUE; //Bool | Whether to display the slide nav arrows or not
    public $slideDots = TRUE; //Bool | Whether to display dots at the bottom of the slide or not (also called nav thumbs)
    public $interval = 3000; //Int | interval between slide changes in milliseconds
    public $pause = 'null'; //String | Set to hover to pause on hover
    public $wrap = TRUE;//Bool | Whether the scroll should wrap to the beginning when it reaches the end.
    public $keyevents = FALSE;//Bool | Whether the carousel should reach to keyboard events
    public $cache = FALSE; //Bool | Do we need to cache images? if no Cache Directory is specified this will add a cache busting string to the end of all image requests.
    public $activeSlideNo = 0;//Int | The slide number of the one we wish to set as active when we start.
    public $leftIcon = 'glyphicon glyphicon-chevron-left'; //String | the icon classes to pass for the left control icon
    public $rightIcon = 'glyphicon glyphicon-chevron-right'; //String | the icon classes to pass for the right control icon
    public $rootDir = DOCUMENT_ROOT; //String | the directory root path for all files used. (normally default is fine).

    private $log; //Logging object, or FALSE if one isn't passed in.
    private $slides; //array of slides in this carousel
    private $slideNo; //number of slides currently on the carousel.
    private $markup; //Carousel markup

    /**
     * Constructor function for each carousel
     * @param string $id
     * @param array $params
     * @param bool|FALSE $log
     */
    public function __construct($id, $params = [], $log = FALSE){
      $this->log = $log;
      $this->id = $id;
      $this->configCarousel($params);
    }

    /**
     * Function to setup the config for this carousel
     * @param $id
     * @param array $params
     * @param bool|FALSE $log
     */
    public function configCarousel($params = [])
    {
      if(!empty($params)) {
        foreach ($params as $propery => $value) {
          $this->{$propery} = $value;
        }
      }
    }

    /**
     * Function to add a slide quickly - if type is not specified in config array, image will be assumed.
     * @param $data
     * @param string $type
     * @param array $config
     * @return $this
     */
    public function addSlide($data, $type = '', $config = []){
      //set a few defaults
      switch($type){
        case 'video':
          $this->_addVideoSlide($data, $config);
          break;
        case 'html':
          $this->_addHtmlSlide($data, $config);
          break;
        default:
        case 'image':
          $this->_addImageSlide($data, $config);
          break;
      }
      return $this;
    }

    /**
     * Public wrapper function for building a carousel. If the markup already exists for this object, don't rebuild it unless expressly told too.
     * @param bool|FALSE $refresh
     * @return $this
     */
    public function getCarousel($refresh = FALSE){
      if($refresh || !isset($this->markup)) {
        $this->_buildCarousel();
      }
      return $this;
    }

    /**
     * Function to get slide data based on the path.
     * @param $path
     * @return $this
     */
    public function getSlide($key){
      return isset($this->slides[$key]) ? $this->slides[$key] : FALSE;
    }

    /**
     * Function to return the markup for the current carousel
     * @return mixed
     */
    public function getMarkup($refresh = FALSE){
      $this->getCarousel($refresh);
      return $this->markup;
    }

    /**
     * Function to build the markup for the carousel.
     * @return $this
     */
    private function _buildCarousel(){
      //Reset any markup that already exists for this carousel and start a new one.
      $this->_markupWrapperOpen()->_markupSlideNavThumbs()->_markupSlides()->_markupSlideControl('left', 'prev', 'Previous')->_markupSlideControl('right', 'next', 'Next')->_markupWrapperClose();
      return $this;
    }

    /**
     * Function to add an image slide to the carousel, this is more verbose than the normal add slide function.
     * @param $image - local path to the image we want to use.
     * @param array $config
     */
    private function _addImageSlide($image, $config)
    {
      $config = $this->_slideDefaults($config);
      $this->slides[$image] = new image($image, $config['attributes'], $config['remote']);
      $this->slides[$image]->slideType = 'image'; //we add this here because the image class doesn't need to know that other types exist.
      $this->slides[$image]->slideTitle = $config['title'];
      $this->slides[$image]->caption = $config['caption'];
      $this->slides[$image]->link = $config['link'];
      $this->slideNo = count($this->slides); //Store the new total number of slides. Allows us to avoid counting in several places.
    }

    private function _addHtmlSlide($html, $config){

    }

    private function _addVideoSlide($video, $config){

    }

    /**
     * Function to set a few defaults in case they arn't already set. Split out here to keep things tidy.
     * @param $config
     * @return mixed
     */
    private function _slideDefaults($config){
      $config['caption'] = isset($config['caption']) ? $config['caption'] : FALSE;
      $config['title'] = isset($config['title']) ? $config['title'] : FALSE;
      $config['link'] = isset($config['link']) ? $config['link'] : FALSE;
      $config['attributes'] = isset($config['attributes']) ? $config['attributes'] : [];
      $config['remote'] = isset($config['remote']) ? $config['remote'] : FALSE;
      $config['attributes']['rootDir'] = isset($config['attributes']['rootDir']) ? $config['attributes']['rootDir'] : $this->rootDir;

      return $config;
    }

    /**
     * Markup for the carousel itself.
     * @return $this
     */
    private function _markupWrapperOpen(){
      $this->markup = '<div id="'.$this->id.'" class="carousel slide" '.$this->slideOnOpen.' data-interval="'.$this->interval.'" data-pause="'.$this->pause.'" data-wrap="'.$this->wrap.'" data-keyboard="'.$this->keyevents.'">';
      return $this;
    }

    /**
     * Close the carousel markup
     * @return $this
     */
    private function _markupWrapperClose(){
      $this->markup .= '</div>';
      return $this;
    }

    /**
     * Function to build the slide thumb nav. Normally internal but pass in the ignore param to ignore current nav settings.
     * @param bool|FALSE $ignore
     * @return $this
     */
    private function _markupSlideNavThumbs($ignore = FALSE){
      if($this->slideDots || $ignore) {
        $this->markup .= '<ol class="carousel-indicators">';
        for ($i = 0; $i < $this->slideNo; ++$i) {
          $this->markup .= '<li data-target="#' . $this->id . '" data-slide-to="' . $i . '" ' . ($this->activeSlideNo == $i ? 'class="active"' : '') . '></li>';
        }
        $this->markup .= '</ol>';
      }
      return $this;
    }

    /**
     * Function to build the markup for each slide in the carousel, depending on type.
     * @return $this
     */
    private function _markupSlides(){
      $this->markup .= '<div class="carousel-inner" role="listbox">';
      $keys = array_keys($this->slides);
      for($i = 0; $i < $this->slideNo; ++$i){
        $slide = $this->slides[$keys[$i]];
        switch($slide->slideType){
          case 'video':
            break;
          case 'html':
            break;
          default:
          case 'image':
            $this->_markupImageSlide($slide, $i);
            break;
        }
      }
      $this->markup .= '</div>';
      return $this;
    }

    /**
     * Function to build the markup for an image slide accepts 2 params, for internal use only.
     * @param $slide
     * @param $slideNo
     * @return $this
     */
    private function _markupImageSlide($slide, $slideNo){
      $this->_markupSlideOpen($slideNo);
      if(isset($slide->link)){ $this->markup .= '<a href="'.$slide->link.'">'; }
      $this->markup .= '<img src="'.$slide->filepath.'" alt="'.$slide->alt.'" title="'.$slide->title.'" width="'.$slide->width.'" height="'.$slide->height.'">';
      if(isset($slide->link)){ $this->markup .= '</a>'; }
      $this->markup .= isset($slide->slideTitle) || isset($slide->caption) ? '<div class="carousel-caption">' : '';
      $this->markup .= isset($slide->slideTitle) ? '<h3>'.$slide->slideTitle.'</h3>' : '';
      $this->markup .= isset($slide->caption) ? '<p>'.$slide->caption.'</p>' : '';
      $this->markup .= isset($slide->slideTitle) || isset($slide->caption) ? '</div>' : '';
      $this->_markupSlideClose();
      return $this;
    }

    /**
     * Function to generate the Nav for the slider.
     * @NOTE inline styling used to allow for font awesome icons without the user having to style them
     * @param $side
     * @param $direction
     * @param string $text
     * @return $this
     */
    private function _markupSlideControl($side, $direction, $text = ''){
      if($this->nav) {
        $this->markup .= '<a class="' . $side . ' carousel-control" href="#' . $this->id . '" role="button" data-slide="' . $direction . '">';
        $this->markup .= '<span class="icon-' . $side . ' ' . $this->{$side.'Icon'} . '" aria-hidden="true" style="top: 50%; position: absolute; text-decoration: none; font-size: 35px; margin-top: -15px;"></span>';
        $this->markup .= '<span class="sr-only">' . $text . '</span>';
        $this->markup .= '</a>';
      }
      return $this;
    }

    /**
     * Function to open a slide, here purely because the same code is used for each slide type - Over optimisation FTW!
     * @param $slideNo
     */
    private function _markupSlideOpen($slideNo){
      $this->markup .= '<div class="item '.($this->activeSlideNo == $slideNo ? 'active' : '').'">';
    }

    /**
     * Function to close each slide, @see _markupSlideOpen();
     */
    private function _markupSlideClose(){
      $this->markup .= '</div>';
    }
  }