<?php

namespace functional;

use Codeception\Util\FileSystem;
use FunctionalTester;

class ProductImportControllerCest
{
    /**
     * @covers \App\Features\ProductsImport\Controllers\BatchImportController::importFormView
     */
    public function importFormViewOpensTest(FunctionalTester $I)
    {
        $I->amOnPage('/import');
        $I->see("Upload source XML", "button[type=submit]");
    }

    /**
     * @covers \App\Features\ProductsImport\Controllers\BatchImportController::importUpload
     */
    public function importUploadWorksTest(FunctionalTester $I)
    {
        $I->amOnPage('/import');
        $tmp = tempnam(__DIR__ . '/../_data/tmp', 'test_') . ".xml";
        $filename = basename($tmp);
        copy(__DIR__ . '/../_data/import_sample.xml', $tmp);
        $I->attachFile('importSource', '/tmp/' . $filename);
        $I->submitForm('#addImportSource', []);
        $I->see("New import source added as Asset", ".alert");
        $I->see($filename, "td");
        $I->attachFile('importSource', '/tmp/' . $filename);
        $I->submitForm('#addImportSource', []);
        $I->see("already exists", ".alert");
        $tmp = tempnam(__DIR__ . '/../_data/tmp', 'test_') . ".xml";
        file_put_contents($tmp, "invalid xml");
        $I->attachFile('importSource', '/tmp/' . basename($tmp));
        $I->submitForm('#addImportSource', []);
        $I->see('File is not recognized as text/xml', ".alert");
        @unlink($tmp);
        FileSystem::deleteDir('/tmp/assets');
    }
}
