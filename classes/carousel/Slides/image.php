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
  public $fileSize;
  public $sizeOutput;
  public $mime;
  public $filename;
  public $mtime;
  public $filepath = ''; //String | Base path for images,
  public $title;
  public $alt;

  private $rootDir; //Probably a better way to do this, but oh well.
  private $remote = FALSE; //Bool | If specified, ALL images will be considered to be remotely loaded. If cacheDir is also specified, these will be saved locally

  public function __construct($path, $attributes = [], $remote = FALSE, $log = FALSE){
    $this->filepath = $path;
    $this->remote = $remote;
    $this->_populate($attributes);
  }

  private function _populate($attributes){
    $this->rootDir = isset($attributes['rootDir']) ? $attributes['rootDir'] : $this->rootDir;
    $filepath = substr($this->filepath, 0, strrpos($this->filepath, '?')); //Clean off query strings
    $imagesize = $filepath ? getimagesize($this->rootDir.$filepath) : getimagesize($this->rootDir.$this->filepath);

    $this->width = isset($attributes['width']) ? $attributes['width'] : $imagesize[0];
    $this->height = isset($attributes['height']) ? $attributes['height'] : $imagesize[1];
    $this->type = $imagesize[2];
    $this->sizeOutput = $imagesize[3];
    $this->fileSize = $filepath ? $this->_fileSize(filesize($this->rootDir.$filepath)) : $this->_fileSize(filesize($this->rootDir.$this->filepath));
    $this->mime = $imagesize['mime'];
    $this->filename = $this->getFilename($this->filepath, $this->remote);
    $this->mtime = $filepath ? filemtime($this->rootDir.$filepath) : filemtime($this->rootDir.$this->filepath);
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

  /**
   * Function to work out file size and apply appropriate units.
   * @see http://stackoverflow.com/a/2510459/2412837
   * @param $bytes
   * @param int $precision
   * @return string
   * @todo put this in a better place since we'll want to use it in other slide classes.
   */
  private function _fileSize($bytes, $precision = 2){
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . ' ' . $units[$pow];
  }
}