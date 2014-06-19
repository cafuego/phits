[![Build Status](https://travis-ci.org/cafuego/phits.svg?branch=master)](https://travis-ci.org/cafuego/phits)

# WELCOME

phits - a FITS reader for PHP
=============================

FITS is the standard astronomical data format endorsed by both NASA and the IAU.

This class can read and parse header data from FITS files. I am not currently
planning to add the ability to read and process the attached data as well.

The FitsThumbnail class uses the Imagick PECL library to convert FITS image
data into a thumbnail of a specified size and format. It will fail horribly
if you do not have Imagick installed.


# USAGE

```
<?php
  use Phits\Fits\FitsParser;

  $fits = new FitsParser('/tmp/foobar.fits');

  $headers = $fits->getHeaders();
  $naxis   = $fits->getNaxis(0);

  // Do stuff.
?>

<?php
  use Phits\Fits\FitsThumbnail;

  $thumb = new FitsThumbnail('/tmp/foobar.fits');

  // Delete the generated thumbnail when PHP exits.
  $thumb->persist(FALSE);

  // Create a 200x200 thumbnail.
  $thumb->createThumbnail(200, 200);

  // Get the generated thumbail file.
  $thumbnail = $thumb->getThumbnail();

  // Do stuff.
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
