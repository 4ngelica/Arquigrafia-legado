<?php namespace lib\image;

use Image;
use File;

class ImageManager {

  public function makeImage($file) {
    return Image::make($file)->encode('jpg', 80);
  }

  public function getOriginalImageExtension($image) {
    return File::extension($image->basePath());
  }

  public function makeOriginal($image, $prefix, $extension) {
    $image->save($prefix . '_original' . $extension);
  }

  public function make200h($image, $prefix) {
    $image->heighten(220)->save($prefix . '_200h.jpg');
  }

  public function makeHome($image, $prefix) {
    $image->fit(186, 124)->encode('jpg', 70)->save($prefix . '_home.jpg');
  }

  public function makeAll($image, $prefix, $extension) {
    $this->makeOriginal($image, $prefix, $extension);
    $this->makeView($image, $prefix);
    $this->make200h($image, $prefix);
    $this->makeHome($image, $prefix);
  }

  public function find($pattern) {
    $file = File::glob( $pattern )[0];
    return File::isFile($file) ? $this->makeImage($file) : null;
  }

}