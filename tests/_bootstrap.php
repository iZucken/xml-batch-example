<?php

// define project root which will be used throughout the bootstrapping process
define('PIMCORE_PROJECT_ROOT', realpath(__DIR__ . '/..'));

// set the used pimcore/symfony environment
putenv('PIMCORE_TEST=1');
putenv('PIMCORE_TEST_DB_DSN="mysql://pimcore_test:pimcore_test@testdb:3307/pimcore_test"');

require_once PIMCORE_PROJECT_ROOT . '/vendor/autoload.php';

\Pimcore\Bootstrap::setProjectRoot();
\Pimcore\Bootstrap::bootstrap();

// add the core pimcore test library to the autoloader - this could also be done in composer.json's autoload-dev section
// but is done here for demonstration purpose
 require_once PIMCORE_PROJECT_ROOT . '/vendor/pimcore/pimcore/tests/_support/Util/Autoloader.php';
 \Pimcore\Tests\Util\Autoloader::addNamespace('Pimcore\Tests', PIMCORE_PROJECT_ROOT . '/vendor/pimcore/pimcore/tests/_support');
