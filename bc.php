<?php

namespace n1ghteyes;

//Load the required classes
use n1ghteyes\bootstrapCarousel\cache;
use n1ghteyes\bootstrapCarousel\carousel;
use n1ghteyes\logging as nl; //load in the logging class.

/**
 * PHP Class to generate the markup for bootstrap carousel's, allows for multiple carousels with different image sources.
 * This class is aimed at Developers and site builders who just want to spit out carousels without worrying about the markup / js requirements.
 * This has been made as flexible as possible, allowing for multiple use cases
 *
 * Supports image caching, remote images, optimised for YSlow scores
 *
 * @author N1ghteyes - www.source-control.co.uk
 * @copyright 2016 N1ghteyes
 * @license license.txt The MIT License (MIT)
 * @link https://github.com/N1ghteyes/bootstrap-carousel-php
 *
 */
class bootstrapCarousel
{

  public $logging; //Bool | Do we want logging?
  public $activeId; //Mixed | The if of the last carousel used, edited, printed or otherwise interacted with

  private $activeCarousel = FALSE; //The currently active carousel object, or false if there isn't one.
  private $carousels = []; //An array of all carousels
  private $log = FALSE; //log object.
  private $refreshActive = FALSE; //This is set to true if new slides are added.

  /**
   * Constructor function - accepts logging params.
   * @param bool|FALSE $logging
   * @param array $log_config
   */
  public function __construct($id = '', $logging = FALSE, $log_config = [])
  {
    //autoload the required classes
    spl_autoload_register(__NAMESPACE__.'\carousel\carousel.php');
    //set a default carousel object as we can't do this on declaration, also fixes IDE errors
    $this->activeCarousel = new carousel($id);
    $this->_logging($logging, $log_config);
    return $this;
  }

  /**
   * Function to add a carousel to the pool of available carousels
   * @param $id
   * @param array $config
   * @return $this
   */
  public function addCarousel($id, $config = [], $logging = FALSE, $log_config = []){
    $this->activeCarousel = new carousel($id, $config);
    if(isset($this->carousels[$this->activeCarousel->id]) && $this->logging) {
      $this->log->ql('A Carousel with the ID of { ' . $this->activeCarousel->id . ' } already existed and has been overwritten');
    }
    $this->carousels[$this->activeCarousel->id] = $this->activeCarousel;
    return $this;
  }

  /**
   * Simply a wrapper for the carousel class.
   * @param $data | Mixed - accepts a string or array of data.
   * @param string $type
   * @param array $config
   * @return $this
   */
  public function addSlide($data, $type = 'image', $config = []){
    //if this is a sting, assume one slide is being added.
    if(is_string($data)){
      $this->activeCarousel->addSlide($data, $type, $config);
      $this->refreshActive = TRUE;
    }

    //if this is an array, assume multiple slides are being added.
    if(is_array($data)){
      //we need to allow for config to be specified for each slide. If the key is an int then the config is the data param.
      //If the key is a string we need to check if config is an array, if it is treat it as the config value
      foreach($data as $key => $config){
        if(is_int($key)){
          $this->activeCarousel->addSlide($config, $type, []);
          $this->refreshActive = TRUE;
        } elseif(is_array($config) && is_string($key)){
          $type = isset($config['type']) ? $config['type'] : $type;
          $this->activeCarousel->addSlide($key, $type, $config);
          $this->refreshActive = TRUE;
        } else {
         if($this->log){
           $this->log->ql('Failed to add slide from array, with key: { ' . $key . ' }');
         }
        }
      }
    }

    return $this;
  }

  /**
   * Uses __get() to autoload the desired carousel, if the value passed matches a known ID. Otherwise, create it.
   * @param $name
   * @return $this
   */
  public function __get($name){
    if($this->activeCarousel && $this->activeCarousel->id == $name){
      //we're already active so no need to do anything here.
      return $this;
    }

    if(isset($this->carousels[$name])){
      //set the carousel we found to active.
      $this->activeCarousel = $this->carousels[$name];
      return $this;
    }
    //if we hit this, we assume its a new carousel.
    $this->addCarousel($name);
    return $this;
  }

  /**
   * Function to set logging
   * @param $logging
   * @param $config
   */
  private function _logging($logging, $config)
  {
    if ($logging && $this->log === FALSE) {
      $this->logging = $logging;
      $this->log = new nl($config);
    }
    return $this;
  }

  /**
   * @param string $id
   * @return bootstrapCarousel
   */
  public function build($id = ''){
    return $this->_getCarouselMarkup($id);
  }

  /**
   * Function to build and output the markup and possibly JS (depending on config) needed to generate a carousel
   * If an ID is passed into this function, the active carousel is changed accordingly.
   * @param string $id
   * @return $this
   */
  private function _getCarouselMarkup($id)
  {
    if(isset($id)){
      if(isset($this->carousels[$id])) {
        $this->activeCarousel = $this->carousels[$id];
        return $this->activeCarousel->getCarousel($this->refreshActive);
      } else {
        if($this->log){
          $this->log->ql('Failed to return markup for the specified carousel ID: { ' . $id . ' }, it doesnt exist');
        }
      }
    }

    return $this->activeCarousel->getMarkup($this->refreshActive);
  }

}