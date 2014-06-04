<?php

namespace Phits\Fits;

interface FitsParserInterface {
  function getHeaders($idx);
  function getNaxis($idx);
}
