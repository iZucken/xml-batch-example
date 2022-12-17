<?php

// define project root which will be used throughout the bootstrapping process
define('PIMCORE_PROJECT_ROOT', realpath(__DIR__ . '/..'));

require_once PIMCORE_PROJECT_ROOT . '/vendor/autoload.php';

\Pimcore\Bootstrap::setProjectRoot();
\Pimcore\Bootstrap::bootstrap();

// add the core pimcore test library to the autoloader - this could also be done in composer.json's autoload-dev section
// but is done here for demonstration purpose
 require_once PIMCORE_PROJECT_ROOT . '/vendor/pimcore/pimcore/tests/_support/Util/Autoloader.php';
 \Pimcore\Tests\Util\Autoloader::addNamespace('Pimcore\Tests', PIMCORE_PROJECT_ROOT . '/vendor/pimcore/pimcore/tests/_support');
