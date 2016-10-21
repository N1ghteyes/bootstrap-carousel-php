<?php
namespace n1ghteyes;
//@todo replace this with auto loading.
include 'classes/cache/cache.php';
include 'classes/carousel/carousel.php';

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
  public $activeMarkup;
  public $activeCarousel = FALSE; //The currently active carousel object, or false if there isn't one.

  private $carousels = []; //An array of all carousels
  private $log = FALSE; //log object.
  private $refreshActive = FALSE; //This is set to true if new slides are added.

  /**
   * Constructor function - accepts logging params.
   * @param bool|FALSE $logging
   * @param array $log_config
   */
  public function __construct($id = FALSE, $logging = FALSE, $log_config = [])
  {
    //set a default carousel object as we can't do this on declaration, also fixes IDE errors
    $this->activeCarousel = new carousel($this->_setSafeId($id));
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
    $this->activeCarousel = new carousel($this->_setSafeId($id), $config);
    if(isset($this->carousels[$this->activeCarousel->id]) && $this->logging) {
      $this->log->ql('A Carousel with the ID of { ' . $this->activeCarousel->id . ' } already existed and has been overwritten');
    }
    $this->carousels[$this->activeCarousel->id] = $this->activeCarousel;
    return $this;
  }

  /**
   * Public wrapper for the carousel config function, allows us to change config for a carousel at any point before output.
   * @param array $config
   * @param string $id
   * @return $this
   */
  public function configCarousel($config = [], $id = FALSE){
    $id = $this->_setSafeId($id);
    if(isset($id) && isset($this->carousels[$id])) {
      $this->carousels[$id]->configCarousel($config);
    } else {
      $this->activeCarousel->configCarousel($config);
    }
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
    $name = $this->_setSafeId($name);
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
  public function build($id = FALSE){
    $this->activeMarkup = $this->_getCarouselMarkup($this->_setSafeId($id));
    return $this;
  }

  /**
   * Impliments __toString magic method to return the markup of the current built banner or if there is none, the active one.
   * @return mixed
   */
  public function __toString()
  {
    //No active markup? better make an attempt to get some.
    if(!isset($this->activeMarkup)){
      $this->build($this->activeCarousel->id);
    }
    return $this->activeMarkup;
  }

  /**
   * Function to build and output the markup and possibly JS (depending on config) needed to generate a carousel
   * @param string $id
   * @return $this
   */
  private function _getCarouselMarkup($id)
  {
    $id = $this->_setSafeId($id);
    if(isset($id)){
      if(isset($this->carousels[$id])) {
        return $this->carousels[$id]->getMarkup($this->refreshActive);
      } else {
        if($this->log){
          $this->log->ql('Failed to return markup for the specified carousel ID: { ' . $id . ' }, it doesnt exist');
        }
      }
    }

    return $this->activeCarousel->getMarkup($this->refreshActive);
  }

  /**
   * Function to make the last passed ID safe.
   * Converts spaces and hyphens to underscores
   * @param $id
   * @return mixed
   */
  private function _setSafeId($id){
      return preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(array(' ', '-'), '_', $id)); // Removes special chars.
  }
}