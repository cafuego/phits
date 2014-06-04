<?php

namespace Phits\Fits;

interface FitsThumbnailInterface {
  function createThumbnail($x, $y, $filename);
  function getThumbnail();
  function setFormat($format);
  function setQuality($quality);
}
