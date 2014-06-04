<?php
/**
 * @file FitsThumbnail.php
 * Get a thumbnail from a FITS file.
 */

namespace Phits\Fits;

/**
 * The FITS thumbnailer!
 *
 * This generates a thumbnail of the specified format. It probably uses the
 * first image blob, in case of a FITS file with multiple images.
 */
class FitsThumbnail implements FitsThumbnailInterface {

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
   * Constructor function.
   *
   * @param $filename
   *   The file path to the FITS file.
   * @param $format
   *   The output format for the thumbnail.
   * @param $quality
   *   The compression level, as a percentage.
   */
  public function __construct($filename, $format = Fits::FORMAT_JPEG, $quality = 60) {
    if (!file_exists($filename)) {
      throw new FitsException('File ' . $filename . ' does not exist.');
    }
    $this->fitsfile = $filename;
    $this->format   = $format;
    $this->quality  = $quality;
  }

  /**
   * Generate a thumbnail in the specified format.
   *
   * @return
   *   The path to the generated thumbnail file.
   */
  public function createThumbnail($x = 150, $y = 150, $filename = null) {
    // Create Imagick object
    $im = new Imagick($this->fitsfile);

    $im->setImageColorspace(255);

    if ($compression = $this->setCompression($format)) {
      $im->setCompression($compression);
      $im->setCompressionQuality($quality);
    }
    $im->setImageFormat($format);

    // Store the size.
    $this->width  = $x;
    $this->height = $y;

    // Resize
    $im->resizeImage($x, $y, Imagick::FILTER_LANCZOS, 1);

    // Generate a unique temporary filename if needed.
    if (empty($filename)) {
      $filename = tempnam(sys_get_temp_dir(), 'FitsThumbnail');
    }

    // Write image on server
    $im->writeImage($filename);
    $im->clear();
    $im->destroy();

    $this->thumbnail = $filename;
  }

  /**
   * Return the thumbnail filename.
   */
  public function getThumbnail() {
    return $this->thumbnail;
  }

  /**
   * Set the thumbnail output format.
   *
   * @param $format
   *   The desired thumbnail image file extension.
   */
  public function setFormat($format) {
    $formats = $this->supportedFormats();
    if (!in_array(strtoupper($format) $formats)) {
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
      return Imagick::COMPRESSION_JPEG
    }
    return NULL;
  }

  /**
   * Ask imagick what formats we support.
   */
  private function supportedFormats() {
    return Imagick::queryFormats();
  }
}
