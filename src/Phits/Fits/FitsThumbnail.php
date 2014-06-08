<?php
/**
 * @file FitsThumbnail.php
 * Get a thumbnail from a FITS file.
 */

namespace Phits\Fits;

use Imagick;

/**
 * The FITS thumbnailer!
 *
 * This generates a thumbnail of the specified format. It probably uses the
 * first image blob, in case of a FITS file with multiple images.
 */
class FitsThumbnail implements FitsThumbnailInterface {

  /**
   * The internal imagemagick object.
   */
  private $im = null;

  /**
   * The FITS filename.
   */
  private $fitsfile = null;

  /**
   * The thumbnail file.
   */
  private $thumbnail = null;

  /**
   * The thumbnail format.
   */
  private $format = null;

  /**
   * The image compression level.
   */
  private $quality = 0;

  /**
   * Thumbnail height.
   */
  private $height = 0;

  /**
   * Thumbnail width.
   */
  private $width = 0;

  /**
   * Should the generated thumbnail persist?
   */
  private $persist = TRUE;

  /**
   * Constructor function.
   *
   * @param $filename
   *   The file path to the FITS file.
   * @param $format
   *   The output format for the thumbnail.
   * @param $quality
   *   The compression level, as a percentage.
   */
  public function __construct($filename, $format = Fits::FITS_FORMAT_JPEG, $quality = 60) {
    if (!file_exists($filename)) {
      throw new FitsException('File ' . $filename . ' does not exist.');
    }
    $this->setFitsFile($filename);
    $this->setFormat($format);
    $this->setQuality($quality);
  }

  /**
   * Tidy up as needed.
   */
  public function __destruct() {
    $this->im->clear();
    $this->im->destroy();

    if (!$this->persist && file_exists($this->thumbnail)) {
      unlink($this->thumbnail);
    }
  }

  /**
   * Set the persistence flag.
   */
  public function persist($persist = TRUE) {
    $this->persist = $persist;
  }

  /**
   * Generate a thumbnail in the specified format.
   *
   * @return
   *   The path to the generated thumbnail file.
   */
  public function createThumbnail($x = 150, $y = 150, $filename = null) {

    if ($compression = $this->setCompression($this->format)) {
      $this->im->setCompression($compression);
      $this->im->setCompressionQuality($this->quality);
    }

    // Mess with the levels. This should be pretty safe for mostly black
    // astro images.
    $this->im->levelImage(0, 0.3, 35535);

    // Store the size.
    $this->width  = $x;
    $this->height = $y;

    // Resize
    $this->im->resizeImage($x, $y, Imagick::FILTER_LANCZOS, 1);

    // Generate a unique temporary filename if needed.
    if (empty($filename)) {
      $filename = tempnam(sys_get_temp_dir(), 'FitsThumbnail') . '.' . $this->format;
    }

    // Set the output format.
    $this->im->setImageFormat($this->format);

    // Write image on server
    $this->im->writeImage($filename);

    $this->thumbnail = $filename;
  }

  /**
   * Return the thumbnail filename.
   */
  public function getThumbnail() {
    return $this->thumbnail;
  }

  /**
   * Set the FITS file and create the imagick object.
   */
  public function setFitsFile($filename) {
    $this->fitsfile = $filename;

    // Create Imagick object
    $this->im = new Imagick($this->fitsfile);
  }

  /**
   * Set the thumbnail output format.
   *
   * @param $format
   *   The desired thumbnail image file extension.
   */
  public function setFormat($format) {
    $formats = $this->supportedFormats();
    if (!in_array(strtoupper($format), $formats)) {
      throw new FitsException('Thumbnail format "' . $format . '" is not supported.');
    }
    $this->format = $format;
  }

  /**
   * Set the output image quality.
   *
   * @param $quality
   *   Set the output image quality (0-100).
   */
  public function setQuality($quality) {
    if (!is_numeric($quality) || $quality < 0 || $quality > 100) {
      throw new FitsException('Quality should be an integer ranging from 0 to 100.');
    }
    $this->quality = $quality;
  }

  /**
   * Set the thumbnail compression type.
   *
   * This depends on the output format, currently only JPEG compression
   * is supported.
   */
  private function setCompression($format) {
    switch ($format) {
    case Fits::FITS_FORMAT_JPEG:
      return Imagick::COMPRESSION_JPEG;
    }
    return NULL;
  }

  /**
   * Ask imagick what formats we support.
   */
  private function supportedFormats() {
    return $this->im->queryFormats();
  }
}
