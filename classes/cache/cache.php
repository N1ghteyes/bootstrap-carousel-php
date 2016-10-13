<?php
namespace n1ghteyes\bootstrapCarousel;

/**
 * PHP class to handle caching for the Bootstrap Carousel library
 *
 * @author N1ghteyes - www.source-control.co.uk
 * @copyright 2016 N1ghteyes
 * @license license.txt The MIT License (MIT)
 * @link https://github.com/N1ghteyes/bootstrap-carousel-php
 * @todo All of this, at the moment its just a dump of code.
 */
  class cache{

    //Additional Class config
    public $cacheType = 'json'; //String | json or apc, defaults to json.
    public $remoteCacheTime = 1209600; //Int | If cache is True, amount of time in seconds to cache remote image data
    public $log = FALSE; //Bool | Add additional debug logging.

    /** Private Properties **/
    //Cache
    private $cacheDir = 'cache'; //String | If this is specified, carousel data will be cached to this DIR in Json Format. If a remote URL is specified images will be saved to this DIR. Must be writable by PHP
    private $cacheData = []; //Array | Array of Json objects for all cached data currently loaded. Key'd by image path

    //Json Storage
    private $jsonString = ''; //String | Json string of loaded json data, if there is any.
    private $jsonFile = ''; //String | Path of the Json cache file to use. Can also be used to build from a Json config file @see $jsonConfig property.
    private $jsonConfig = FALSE; //Bool | are we loading from a jsonConfig file? If yes, we don't write out to it.

    public function __construct(){

    }

    private function _cache($image, $remote){
      //first we need to know how we're caching the data
      $this->cacheData = $this->cacheType == 'json' ? $this->_jsonCache() : $this->_apcCache();
    }

    private function _processCache($image, $remote)
    {


      //do we have a cache record already? and if so, is it still valid?
      if(isset($this->jsonData[$image->path]) && isset($this->jsonData[$image->path]->lastModified) && $this->jsonData[$image->path]->lastModified == filemtime($image->path)){
        //We can use the image cache for this, so just set all image properties to the cached values.
        $image = $this->jsonData[$image->path];
      } else {
        //We don't have an active cache record or its invalid so build the object
        //first we check if this is a remote image, if it is we need to grab it and cache it.
        if($remote){
          if($imagedata = file_get_contents($image->path)){
            $image->filename = $this->getFilename($image->path, TRUE);
            if(!file_put_contents($this->cacheDir.'/remoteImages/'.$image->filename, $imagedata)){
              if($this->log){
                $this->logData[__FUNCTION__][] = 'Failed to save the remote to cache at {' . $image->path . '} with the filename {' . $image->filename . '}';
              }
            } else {
              //We managed to store the remote image locally, so lets get on with configuring the image properties
              $this->_populateImageObject($image);
            }
          } else {
            if($this->log){
              $this->logData[__FUNCTION__][] = 'Failed to fetch the remote image at {' . $image->path . '}';
            }
          }
        } else {
          $image->filename = $this->getFilename($image->path);
          $this->_populateImageObject($image);
        }
        $this->jsonData[$image->path] = $image;
      }


      if ($this->jsonConfig === TRUE) {
        $this->_populateJson(FALSE);
      }
    }

    private function _populateJson($generateFile, $loadFromData = FALSE)
    {


    }
  }