<?php
/**
 * @file FitsParser.php
 * Parses a FITS file into a structured object.
 */

namespace Phits\Fits;

/**
 * The FITS parser!
 */
class FitsParser implements FitsInterface {

  /**
   * Array of FITS header blocks.
   */
  private $hdus = [];

  /**
   * The naxis data for each header block.
   */
  private $naxis = [];

  /**
   * Constructor function.
   */
  public function __construct($filename) {
    $fp = fopen($filename, 'r');
    if (empty($fp)) {
      throw new \Exception('Could not open file: ' . $filename);
    }
    $this->parse($fp);
    fclose($fp);
  }

  /**
   * Return the headers.
   *
   * @param $idx
   *   Return the header block at offset $idx or all headers if null.
   * @return
   *   An array of headers.
   */
  public function getHeaders($idx = NULL) {
    return ($idx == NULL) ? $this->hdus : $this->hdus[$idx];
  }

  /**
   * Return the naxis.
   *
   * @param $idx
   *   Return the naxis data at offset $idx or all naxis data if null.
   * @return
   *   An array of naxis data.
   */
  public function getNaxis($idx = NULL) {
    return ($idx == NULL) ? $this->naxis : $this->naxis[$idx];
  }

  /**
   * Parse the file and populate the internal header and naxis structures.
   */
  private function parse($fp) {
    // Flag whether we're done reading.
    $end = FALSE;

    do {
      $previous = $key = '';
      $keys = [];
      $naxis = [];

      $block = fread($fp, Fits::FITS_BLOCK_LENGTH);

      // Read the headers in each block.
      for ($i = 0; $i < Fits::FITS_BLOCK_LINES; $i++) {
        $line = substr($block, $i * Fits::FITS_LINE_LENGTH, ($i+1) * Fits::FITS_LINE_LENGTH);

        $previous = $key;
        $key = trim(substr($line, 0, 8));

        if (empty($key)) {
          continue;
        }

        if (!preg_match('/^[A-Z0-9_-]{1,8}$/', $key)) {
          throw new \Exception('Invalid header "' . $key . '" in file.');
        }

        $sep = substr($line, 8, 10);
        if (strpbrk($sep, '= _') === FALSE) {
          $keys[$key] = NULL;
          continue;
        }

        $val = trim(substr($line, 10));
        if (empty($val)) {
          $keys[$key] = NULL;
          continue;
        }

        $first = $val{0};

        if ($first == '\'') {
          $val = $this->processString($val);
          if ($key == 'CONTINUE') {
            $key = $previous;
            $keys[$key] .= $val;
          }
          else {
            if (!empty($keys[$key])) {
              $keys[$key] .= $val;
            }
            else {
              $keys[$key] = $val;
            }
          }
          continue;
        }

        if ($first == '_') {
          $val = $this->processString($val);
          if ($key == 'CONTINUE') {
            $keys[$previous] .= $val;
          }
          else {
            $keys[$key] = $val;
          }
          continue;
        }

        $sep = strpos($val, '/');
        if ($sep !== FALSE) {
          $val = substr($val, 0, $sep);
        }

        $value = trim($val);
        if (empty($value)) {
          $keys[$key] = NULL;
          continue;
        }

        if (($first >= '0' && $first <= '9') || $first == '+' || $first == '-') {
          if (strpbrk($value, '.DE') !== FALSE) {
            $value = strtr($value, array('D' => 1, 'E' => 1));
            $keys[$key] = floatval($value);
          }
          else {
            $keys[$key] = intval($value);
          }
        }
        else if ($first == 'T') {
          $keys[$key] = TRUE;
        }
        else if ($first == 'F') {
          $keys[$key] = FALSE;
        }
        else if ($first == '(') {
          $ret = sscanf($value, '(%f,%f)', $x, $y);
          $keys[$key] = array($x, $y);
        }
      }

      // Are we there yet?
      $end = array_key_exists('END', $keys);

      // Store the extracted headers for the block.
      $this->hdus[] = $keys;

    } while (!feof($fp) && !$end);

    // Populate naxis, but only if it exists for the header group.
    foreach ($this->hdus as $hdu) {
      $naxis = [];
      if (!empty($hdu['NAXIS'])) {
        $n = intval($hdu['NAXIS']);
        for ($i = 1; $i <= $n; $i++) {
          $naxis[$i] = intval($hdu['NAXIS' . $i]);
        }
      }
      $this->naxis[] = $naxis;
    }

  }

  /**
   * Tiny state machine to extract string values.
   */
  private function processString($s) {
    $buf   = '';
    $state = 0;

    for ($i = 0; $i < strlen($s); $i++) {
      $quote = ($s{$i} == '\'');

      switch ($state) {

      case 0:
        if (!$quote) {
          throw new \Exception('String "' . $s .'" does not start with a quote.');
        }
        $state = 1;
        break;

      case 1:
        if ($quote) {
          $state = 2;
        }
        else {
          $buf .= $s{$i};
          $state = 1;
        }
        break;
      case 2:

        if ($quote) {
          $buf .= $s{$i};
          $state = 1;
        }
        else {
          return trim($buf);
        }
      }

    }
    throw new \Exception('String "' . $s . '" ends prematurely.');
  }

}
