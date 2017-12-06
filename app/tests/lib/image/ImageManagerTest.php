<?php

use lib\image\ImageManager;
use Mockery as m;

class ImageManagerTest extends TestCase {
  
  protected $manager;

  public function tearDown()
  {
    m::close();
  }

  public function setUp() 
  {
    parent::setUp();
    $this->manager = new ImageManager;
  }

  public function testShouldMakeImage() {
    $image = m::mock('Intervention\Image\Image');
    $image->shouldReceive('encode')->once()->andReturn($image);
    Image::shouldReceive('make')->once()->with('file')->andReturn($image);
    $return = $this->manager->makeImage('file');

    $this->assertEquals($image, $return);
  }

  public function testShouldReturnExtension() {
    $image = m::mock('Intervention\Image\Image');
    $image->shouldReceive('basePath')->once()->andReturn('image_basepath');
    File::shouldReceive('extension')->once()->with('image_basepath')->andReturn('image_extension');
    $return = $this->manager->getOriginalImageExtension($image);

    $this->assertEquals('image_extension', $return);
  }

  public function testShouldFindImageFromPattern() {
    File::shouldReceive('glob')->once()->with('pattern')->andReturn(array('file'));
    File::shouldReceive('isFile')->once()->with('file')->andReturn(true);
    $manager = m::mock('lib\image\ImageManager[makeImage]');
    $manager->shouldReceive('makeImage')->once()->with('file')->andReturn('image');
    $return = $manager->find('pattern');

    $this->assertEquals('image', $return);

  }
}