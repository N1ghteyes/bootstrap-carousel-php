<?php
namespace n1ghteyes\bootstrapCarousel\cache\json;

/**
 * PHP class to handle json caching for the Bootstrap Carousel library
 *
 * @author N1ghteyes - www.source-control.co.uk
 * @copyright 2016 N1ghteyes
 * @license license.txt The MIT License (MIT)
 * @link https://github.com/N1ghteyes/bootstrap-carousel-php
 * @todo All of this, at the moment its just a dump of code.
 */
 class jsonCache{

   function __construct(){

   }

   /**
    * Function to handle jsonCache storage
    */
   private function _jsonCache(){
     //First check if we've already got a json file defined
     $this->jsonFile = !isset($this->jsonFile) && $this->jsonConfig === FALSE ? $this->cacheDir . '/' . $this->id . '.json' : $this->jsonFile;
     //Next, do we already have cache data loaded? If not, we need to check if the file exists before loading it.

     if (isset($this->jsonFile) && is_readable($this->jsonFile)) {
       //load the contents of the cache file and set it to jsonData
       $this->jsonData = json_decode(file_get_contents($this->cacheDir . '/' . $this->id . '.json'));
       //Looks like the file isn't readable, next check.
     } elseif (isset($this->jsonFile) && is_dir($this->cacheDir)) {
       $this->jsonData = false; //Directory exists,  file isn't readable, log it just in case.
       //if logging is enabled, log this.
       if ($this->log) {
         $this->logData[__FUNCTION__][] = 'The directory {' . $this->cacheDir . '} exists, however the file {' . $this->id . '.json' . '} doesn\'t exist or isn\'t readable';
       }
     } elseif (isset($this->jsonFile) && mkdir($this->cacheDir, 0755, true)) {
       //we've created the dir, so there is no data to set yet,
       $this->jsonData = false;
       //If we hit this then there really is a problem, log it.
     } elseif ($this->log) {
       $this->logData[__FUNCTION__][] = 'The provided Json file at {' . $this->jsonFile . '}  isn\'t readable or doesnt exist.';
     }
   }

   /**
    * Function to load carousel data from a predefined json file or string
    * @param $jsonData String, Either a file path or a string of json Data
    * @param $file
    */
   public function useJsonConfig($jsonData, $file = TRUE)
   {
     $this->jsonConfig = TRUE;
     if ($file) {
       $this->jsonFile = $jsonData;
       $this->_populateJson(FALSE);
     } else {
       $this->jsonString = $jsonData;
       $this->jsonData = json_decode($jsonData);
       $this->_populateJson(FALSE, TRUE);
     }
   }

   public function addDirectory($path)
   {

   }
 }