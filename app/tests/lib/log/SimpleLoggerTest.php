<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use lib\log\SimpleLogger;
use Mockery as m;

class SimpleLoggerTest extends TestCase {
  
  protected $logger;

  public function tearDown()
  {
    m::close();
  }

  public function setUp()
  {
    parent::setUp();
    $this->logger = App::make('lib\log\SimpleLogger');
  }

  public function testShouldReturnInstanceOfLogger() {
    $return = $this->logger->newLogger('fake_logger');

    $this->assertTrue( $return instanceof Logger );
  }

  public function testShouldDefineRootForLogger() {
    $root = storage_path() . '/logs/test/';
    $this->logger->setRoot('test', true);

    $this->assertEquals( $root, $this->logger->getRoot() );
  }

  public function testShouldCreateNewHandler() {
    $return = $this->logger->newHandler('fake_file');

    $this->assertTrue($return instanceof StreamHandler);
  }

  public function testShouldCreateRootFolder() {
    File::shouldReceive('exists')->once()->andReturn(false);
    File::shouldReceive('makeDirectory')->once();

    $this->logger->createRootFolder();
  }

  public function testShouldNotCreateRootFolder() {
    File::shouldReceive('exists')->once()->andReturn(true);
    File::shouldNotReceive('makeDirectory');

    $this->logger->createRootFolder();
  }

  public function testShouldCreateFileLog() {
    File::shouldReceive('exists')->once()->andReturn(false);
    File::shouldReceive('put')->once();

    $this->logger->createFileLog('fake_filename');
  }

  public function testShouldPushHandler() {
    $logger = m::mock('lib\log\SimpleLogger' .
        '[getFilePath, createFileLog, newHandler, newLogger, setRoot, createRootFolder]');

    $logger->shouldReceive('getFilePath')->once()
      ->with('file_name')->andReturn('file_path');

    $logger->shouldReceive('createFileLog')->once()->with('file_path');

    $logger->shouldReceive('newHandler')->once()->with('file_path')->andReturn('handler');

    $monolog_logger = m::mock('Logger');

    $monolog_logger->shouldReceive('pushHandler')->once()->with('handler');
    
    $logger->shouldReceive('newLogger')->once()->andReturn($monolog_logger);

    $logger->shouldReceive('setRoot')->once();

    $logger->shouldReceive('createRootFolder')->once();

    $logger->init('logger_name', 'log_folder');
    $logger->logToFile('file_name');

  }

  public function testShouldLogAllLevels() {
    $monolog_logger = m::mock('Logger');
    
    $logger = m::mock('lib\log\SimpleLogger' .
        '[newLogger, setRoot, createRootFolder]');
    
    $logger->shouldReceive('newLogger')
      ->once()->andReturn($monolog_logger);
    
    $logger->shouldReceive('setRoot')
      ->once();

    $logger->shouldReceive('createRootFolder')
      ->once();

    $monolog_logger->shouldReceive('addInfo')
      ->once();

    $monolog_logger->shouldReceive('addNotice')
      ->once();

    $monolog_logger->shouldReceive('addWarning')
      ->once();

    $monolog_logger->shouldReceive('addError')
      ->once();

    $logger->init('fake_logger_name', 'fake_log_folder');

    $logger->addInfo('message');
    $logger->addNotice('message');
    $logger->addWarning('message');
    $logger->addError('message');
  }

}