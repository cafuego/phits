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
  }

  /**
   * Test the NAXIS header matches the naxis data.
   */
  public function testNaxisData() {
    $file = dirname(__FILE__) . '/data/testkeys.fits';
    $fits = new FitsParser($file);

    // Check the naxis block contains as many values as the header says.
    $headers = $fits->getHeaders(0);
    $naxis = $fits->getNaxis();
    $wanted = $headers[0]['NAXIS'];

    $block = count($naxis[0]);
    $this->assertEquals($block, $wanted, 'Found correct number of naxis in first block.');
  }

  /**
   * Test the data in a real FITS file (from BRT).
   */
  public function testFileData() {
    $file = dirname(__FILE__) . '/data/brt-210055.fits';
    $fits = new FitsParser($file);

    $headers = $fits->getHeaders();

    $this->assertEquals($headers[0]['OBJECT'], 'M8', 'File contains Lagoon nebula data.');
    $this->assertEquals($headers[0]['TELESCOP'], 'BRT Galaxy Camera', 'Data from BRT Galaxy Camera.');
  }
}
