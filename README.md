
NOTE: Site migrate to new server late Jan 2018 - new server is NGINX so .htaccess files don't work
Also note sim links need to be created in:

/srv/www/wp.dxlab.sl.nsw.gov.au/shared/experiments/mainstreet/web/bundles

they are:

framework -> ../../vendor/symfony/symfony/src/Symfony/Bundle/FrameworkBundle/Resources/public/
sensiodistribution -> ../../vendor/sensio/distribution-bundle/Sensio/Bundle/DistributionBundle/Resources/public/
sldataharvest -> ../../src/SL/DataHarvestBundle/Resources/public/ 

and
/srv/www/wp.dxlab.sl.nsw.gov.au/shared/experiments/mainstreet/bin

they are:

doctrine -> ../vendor/doctrine/orm/bin/doctrine
doctrine.php -> ../vendor/doctrine/orm/bin/doctrine.php
security-checker -> ../vendor/sensiolabs/security-checker/security-checker

To avoid some issues with non-https access of SLNSW images from the ACMS site we copied the images onto the server and updated the XML file SLNSWMainStreets.xml which lives in /srv/www/wp.dxlab.sl.nsw.gov.au/shared/experiments/mainstreet/web/bundles/sldataharvest/xml to have relative paths to the local images instead of URLs to the ACMS site.

The images live in a folder called acms which lives in /srv/www/wp.dxlab.sl.nsw.gov.au/shared/experiments/mainstreet/web

The site only seems to work with this URL: dxlab.sl.nsw.gov.au/mainstreet/web/app.php rather than the dxlab.sl.nsw.gov.au/mainstreet/ or dxlab.sl.nsw.gov.au/mainstreet/web/ 
So we also two index.php files in those location which re-direct to dxlab.sl.nsw.gov.au/mainstreet/web/app.php

Lastly the lack of .htaccess files seems to stop some ajax calls to Trove from working meaning the tags that normally appear in the middle of the page do not show up. This seems to be because URLs of this form do not work: 

https://dxlab.sl.nsw.gov.au/mainstreet/web/app.php/filter_tags/1887?callback=window.jsonpCallbacks.callback2&_=1517452960459

Not sure how to fix this right now.


Symfony Standard Edition
========================

Welcome to the Symfony Standard Edition - a fully-functional Symfony2
application that you can use as the skeleton for your new applications.

For details on how to download and get started with Symfony, see the
[Installation][1] chapter of the Symfony Documentation.

What's inside?
--------------

The Symfony Standard Edition is configured with the following defaults:

  * An AppBundle you can use to start coding;

  * Twig as the only configured template engine;

  * Doctrine ORM/DBAL;

  * Swiftmailer;

  * Annotations enabled for everything.

It comes pre-configured with the following bundles:

  * **FrameworkBundle** - The core Symfony framework bundle

  * [**SensioFrameworkExtraBundle**][6] - Adds several enhancements, including
    template and routing annotation capability

  * [**DoctrineBundle**][7] - Adds support for the Doctrine ORM

  * [**TwigBundle**][8] - Adds support for the Twig templating engine

  * [**SecurityBundle**][9] - Adds security by integrating Symfony's security
    component

  * [**SwiftmailerBundle**][10] - Adds support for Swiftmailer, a library for
    sending emails

  * [**MonologBundle**][11] - Adds support for Monolog, a logging library

  * [**AsseticBundle**][12] - Adds support for Assetic, an asset processing
    library

  * **WebProfilerBundle** (in dev/test env) - Adds profiling functionality and
    the web debug toolbar

  * **SensioDistributionBundle** (in dev/test env) - Adds functionality for
    configuring and working with Symfony distributions

  * [**SensioGeneratorBundle**][13] (in dev/test env) - Adds code generation
    capabilities

  * **DebugBundle** (in dev/test env) - Adds Debug and VarDumper component
    integration

All libraries and bundles included in the Symfony Standard Edition are
released under the MIT or BSD license.

Enjoy!

[1]:  https://symfony.com/doc/2.7/book/installation.html
[6]:  https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/index.html
[7]:  https://symfony.com/doc/2.7/book/doctrine.html
[8]:  https://symfony.com/doc/2.7/book/templating.html
[9]:  https://symfony.com/doc/2.7/book/security.html
[10]: https://symfony.com/doc/2.7/cookbook/email.html
[11]: https://symfony.com/doc/2.7/cookbook/logging/monolog.html
[12]: https://symfony.com/doc/2.7/cookbook/assetic/asset_management.html
[13]: https://symfony.com/doc/2.7/bundles/SensioGeneratorBundle/index.html
