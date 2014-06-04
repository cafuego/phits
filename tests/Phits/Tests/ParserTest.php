<?php

namespace Phits\Tests;

use Phits\Fits\FitsParser;

class TestFits Extends \PHPUnit_Framework_TestCase {

  /**
   * Check for parser errors on a valid file.
   */
  public function testValidFitsFile() {
    $error = NULL;

    $file = dirname(__FILE__) . '/data/testkeys.fits';
    try {
      $fits = new FitsParser($file);
    } catch (\Exception $e) {
      $error = $e->getMessage();
    }
    $this->assertNull($error, 'No error whilst parsing file.');
  }

  /**
   * Check for parser errors on an invalid file.
   */
  public function testBadFitsFile() {
    $error = NULL;
    $file = dirname(__FILE__) . '/data/testkeys2.fits';
    try {
      $fits = new FitsParser($file);
    } catch (\Exception $e) {
      $error = $e->getMessage();
    }
    $this->assertEquals($error, 'Invalid header "KEY.NAME" in file.', 'Found the expected file error.');
  }

  /**
   * Check for fetched headers.
   */
  public function testHeaders() {
    $file = dirname(__FILE__) . '/data/testkeys.fits';
    $fits = new FitsParser($file);

    $headers = $fits->getHeaders();
    $this->assertNotNull($headers, 'Retrieved headers from file.');

    $count = count($headers);
    $this->assertEquals($count, 5, 'Loaded 5 header blocks.');

    $block = count($headers[0]);
    $this->assertEquals($block, 20, 'Found 20 headers in first block.');
  }

  /**
   * Check for fetched naxis.
   */
  public function testNaxis() {
    $file = dirname(__FILE__) . '/data/testkeys.fits';
    $fits = new FitsParser($file);

    $naxis = $fits->getNaxis();
    $this->assertNotNull($naxis, 'Retrieved naxis from file.');

    $count = count($naxis);
    $this->assertEquals($count, 5, 'Loaded 5 naxis blocks.');

    $block = count($naxis[0]);
    $this->assertEquals($block, 2, 'Found 2 naxis in first block.');
  }
}
