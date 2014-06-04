<?php

namespace Phits\Tests;

use Phits\Fits\FitsThumbnail;

class TestThumbnail Extends \PHPUnit_Framework_TestCase {

  /**
   * Check for parser errors on a valid file.
   */
  public function testThumbJPG() {
    $file = dirname(__FILE__) . '/data/brt-210055.fits';
    $thumb = new FitsThumbnail($file);

    $thumb->setFormat('JPG');
    $thumb->createThumbnail(200, 200);

    $thumbfile = $thumb->getThumbnail();

    // Check that the thumbnail exists.
    $this->assertTrue(file_exists($thumbfile), 'Generated a thumbnail.');

    $thumb->persist(FALSE);
    unset($thumb);

    // Check that the thumbnail file is really gone.
    $this->assertFalse(file_exists($thumbfile), 'Deleted an ephemeral thumbnail.');

  }
}
