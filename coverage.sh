#!/bin/bash
rm -rf public/coverage
APP_ENV=test APP_DEBUG=true PIMCORE_DEV_MODE=true PIMCORE_ENVIRONMENT=test XDEBUG_MODE=coverage vendor/bin/codecept run -c . --coverage --coverage-html
mv tests/_output/coverage public/coverage