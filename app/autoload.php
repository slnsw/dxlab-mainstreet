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

return $loader;
