<?php

namespace functional;

use Codeception\Util\FileSystem;
use FunctionalTester;

class ProductImportControllerCest
{
    /**
     * @covers \App\Features\ProductsImport\Controllers\BatchImportController::__construct
     * @covers \App\Features\ProductsImport\Controllers\BatchImportController::importFormView
     */
    public function importFormViewOpensTest(FunctionalTester $I)
    {
        $I->amOnPage('/import');
        $I->seeElement("form#addImportSource");
        $I->seeElement("table#importLogTable");
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
        $I->seeResponseCodeIs(200);
        $I->see("Import process started on \"$filename\"");
        $I->amOnPage('/import');
        $I->attachFile('importSource', '/tmp/' . $filename);
        $I->submitForm('#addImportSource', []);
        $I->seeResponseCodeIs(400);
        $I->see("File \"$filename\" already exists in the storage");
        $tmp = tempnam(__DIR__ . '/../_data/tmp', 'test_') . ".xml";
        file_put_contents($tmp, "invalid xml");
        $I->amOnPage('/import');
        $I->attachFile('importSource', '/tmp/' . basename($tmp));
        $I->submitForm('#addImportSource', []);
        $I->seeResponseCodeIs(400);
        $I->see("File is not recognized as text/xml");
        $I->amOnPage('/import');
        $I->submitForm('#addImportSource', []);
        $I->seeResponseCodeIs(400);
        $I->see("No file");
        @unlink($tmp);
        FileSystem::deleteDir('/tmp/assets');
    }

    /**
     * @covers \App\Features\ProductsImport\Controllers\BatchImportController::importUpload
     */
    public function importUploadWorksWithSpacesInFilenameTest(FunctionalTester $I)
    {
        $I->amOnPage('/import');
        $tmp = tempnam(__DIR__ . '/../_data/tmp', 'test with spaces ') . ".xml";
        $filename = basename($tmp);
        copy(__DIR__ . '/../_data/import_sample.xml', $tmp);
        $I->attachFile('importSource', '/tmp/' . $filename);
        $I->submitForm('#addImportSource', []);
        $I->seeResponseCodeIs(200);
        $I->see("Import process started on \"$filename\"");
    }
}
