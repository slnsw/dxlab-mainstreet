<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

// Autoload the primary eHive class prefixed with EHive.
$loader->add('EHive_', __DIR__.'/../vendor/ehive/lib');

// Autoload the primary zebra class prefixed with Zebra.
$loader->add('Zebra_', __DIR__.'/../vendor/zebra_curl/lib');

return $loader;