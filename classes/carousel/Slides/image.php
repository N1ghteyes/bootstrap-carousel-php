<?php
namespace n1ghteyes\bootstrapCarousel\carousel;

/**
 * PHP class to handle image processing for the Bootstrap Carousel library
 *
 * @author N1ghteyes - www.source-control.co.uk
 * @copyright 2016 N1ghteyes
 * @license license.txt The MIT License (MIT)
 * @link https://github.com/N1ghteyes/bootstrap-carousel-php
 *
 * @todo add remote image support
 */
  class image{

    public $height;
    public $width;
    public $type;
    public $size;
    public $mime;
    public $filename;
    public $mtime;
    public $path = ''; //String | Base path for images,
    public $title;
    public $alt;

    private $remote = FALSE; //Bool | If specified, ALL images will be considered to be remotely loaded. If cacheDir is also specified, these will be saved locally

    public function __construct($path, $attributes = [], $remote = FALSE, $log = FALSE){
      $this->path = $path;
      $this->remote = $remote;
      $this->_populate($attributes);
    }

    private function _populate($attributes){
      $imagesize = getimagesize($this->path);

      $this->width = isset($attributes['width']) ? $attributes['width'] : $imagesize[0];
      $this->height = isset($attributes['height']) ? $attributes['height'] : $imagesize[1];
      $this->type = $imagesize[2];
      $this->sizeOutput = $imagesize[3];
      $this->mime = $imagesize['mime'];
      $this->filename = $this->getFilename($this->path, $this->remote);
      $this->mtime = filemtime($this->path);
      $this->title = isset($attributes['title']) ? $attributes['title'] : $this->filename;
      $this->alt = isset($attributes['alt']) ? $attributes['alt'] : $this->filename;
    }

    /**
     * Simple function to return the filename for the path passed in.
     * @param $path
     * @param bool|FALSE $remote
     * @return string
     */
    public function getFilename($path, $remote = FALSE){
      if($remote){
        return basename(parse_url($path, PHP_URL_PATH));
      } else {
        $pos = strrpos($path, '/');
        return  $pos === false ? $path : substr($path, $pos + 1);
      }
    }
  }