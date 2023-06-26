#!/bin/bash
set -ex
APP_ENV=test APP_DEBUG=true PIMCORE_DEV_MODE=true PIMCORE_ENVIRONMENT=test vendor/bin/codecept run -c . -f