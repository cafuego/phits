<?php

namespace Phits\Fits;

interface FitsInterface {
  function getHeaders($idx);
  function getNaxis($idx);
}
