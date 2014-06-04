<?php
/**
 * @file Fits.php
 * Just stores stuffs.
 */

namespace Phits\Fits;

/**
 * A basic class.
 */
class Fits {

  // A header block is 2880 bytes.
  const FITS_BLOCK_LENGTH = 2880;

  // A header is 80 bytes.
  const FITS_LINE_LENGTH  = 80;

  // A header block can contain up to 36 headers.
  const FITS_BLOCK_LINES  = 36;

  // The JPEG thumbnail format.
  const FITS_FORMAT_JPEG = 'JPG';

  // The PNG thumbnail format.
  const FITS_FORMAT_PNG  = 'PNG';

}
