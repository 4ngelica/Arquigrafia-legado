<?php

use Mockery as m;

class OdsFileSearcherTest extends TestCase {

  protected $searcher;

  public function tearDown()
  {
    m::close();
  }

  public function setUp()
  {
    parent::setUp();
    $this->searcher = App::make('lib\photoimport\ods\OdsFileSearcher');
  }

  public function testGetAllFiles() {
    $file = 'file';
    $allFiles = 'allFiles';
    File::shouldReceive('allFiles')->with($file)->andReturn($allFiles);
    $return = $this->searcher->getAllFiles($file);
    
    $this->assertEquals($allFiles, $return);
  }



  public function testSearchReturnsAllValidOdsFiles() {
    $searcher = m::mock('lib\photoimport\ods\OdsFileSearcher[getAllFiles, newOds]');
    $root = m::mock('odsFilesRootPath');

    $file1 = m::mock('SplFileInfo');
    $file1->shouldReceive('isFile')->once()->andReturn(true);
    $file1->shouldReceive('getExtension')->once()->andReturn('ods');
    $file1->shouldReceive('getPathname')->once()->andReturn('file_log_doesnt_exist');

    $file2 = m::mock('SplFileInfo');
    $file2->shouldReceive('isFile')->once()->andReturn(true);
    $file2->shouldReceive('getExtension')->once()->andReturn('ods');
    $file2->shouldReceive('getPathname')->once()->andReturn('file_log_exists');
    

    $files = array( $file1, $file2 );

    File::shouldReceive('exists')->once()->with('file_log_doesnt_exist.log')->andReturn(false);
    File::shouldReceive('exists')->once()->with('file_log_exists.log')->andReturn(true);

    $searcher->shouldReceive('getAllFiles')->with($root)->andReturn($files);
    $searcher->shouldReceive('newOds')->once()->with($file1)->andReturn($file1);
    $return = $searcher->search($root);
    
    $this->assertEquals( array($file1), $return );
  }
}
