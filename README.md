phits - a FITS reader for PHP
=============================

FITS is the standard astronomical data format endorsed by both NASA and the IAU.

This class can read and parse header data from FITS files. I am not currently
planning to add the ability to read and process the attached data as well,
though I might add a wrapper class that can convert FITS image data to TIFF
using ImageMagick.


USAGE
-----
```
<?php
  use Phits\Fits\FitsParser;

  $fits = new FitsParser('/tmp/foobar.fits');

  $headers = $fits->getHeaders();
  $naxis   = $fits->getNaxis(0);
?>
```

ACKNOWLEDGEMENTS
----------------
This project is based on pre-existing open source FITS libraries and I have
used both of them to help me write this parser:

* https://github.com/astrojs/fitsjs
* https://github.com/siravan/fits

Phits inherits the MIT license from these other projects.

As opposed to the Go parser, Phits also handles CONTINUE headers for longer
string comments.

FITS?
-----
See http://heasarc.nasa.gov/docs/heasarc/fits.html for more information.
