<?php

namespace functional;

use FunctionalTester;
use Symfony\Component\Uid\Uuid;

/**
 * @coversDefaultClass \App\Features\ImportLogs\Controllers\ImportLogController
 */
class ImportLogControllerCest
{
    /**
     * @covers ::__construct
     * @covers ::index
     */
    public function indexTest(FunctionalTester $I)
    {
        $I->amOnPage('/import');
        $I->sendAjaxGetRequest('/importLog/datatable', []);
        $I->seeResponseCodeIs(200);
        $I->see('{"draw":null,"recordsTotal":0,"recordsFiltered":0,"data":[]}');
        $I->wantToTest("that new xml upload adds report row to api response");
        $I->amOnPage('/import');
        $tmp = tempnam(__DIR__ . '/../_data/tmp', 'test_') . ".xml";
        $filename = basename($tmp);
        copy(__DIR__ . '/../_data/import_sample.xml', $tmp);
        $I->attachFile('importSource', '/tmp/' . $filename);
        $I->submitForm('#addImportSource', []);
        $I->seeResponseCodeIs(200);
        $I->see("Import process started on \"$filename\"");
        sleep(1);
        $I->sendGet('/importLog/datatable');
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(200);
        $I->assertCount(1, $I->grabDataFromResponseByJsonPath("$..data.[0].uuid"));
    }

    /**
     * @covers ::downloadCsv
     */
    public function downloadCsvTest(FunctionalTester $I)
    {
        $I->wantToTest("arbitrary id gives 404");
        $I->amOnPage('/importLog/'.Uuid::v4().'/csv');
        $I->seeResponseCodeIs(404);

        $I->wantToTest("that previous xml upload resulted in 10 records created and recorded in stats");
        $I->sendGet('/importLog/datatable');
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(200);
        $uuid = $I->grabDataFromResponseByJsonPath("$..data.[0].uuid");
        $I->amOnPage('/importLog/'.$uuid[0].'/csv');
        $I->seeResponseCodeIs(200);
        $I->see("discovered,created,updated,malformed\n10,10,0,0");

        $I->wantToTest("that re-uploading xml results in no new records");
        $I->amOnPage('/import');
        $tmp = tempnam(__DIR__ . '/../_data/tmp', 'test_') . ".xml";
        $filename = basename($tmp);
        copy(__DIR__ . '/../_data/import_sample.xml', $tmp);
        $I->attachFile('importSource', '/tmp/' . $filename);
        $I->submitForm('#addImportSource', []);
        $I->seeResponseCodeIs(200);
        $I->see("Import process started on \"$filename\"");
        sleep(1);
        $I->sendGet('/importLog/datatable');
        $I->seeResponseIsJson();
        $I->seeResponseCodeIs(200);
        $uuid = $I->grabDataFromResponseByJsonPath("$..data.[0].uuid");
        $I->amOnPage('/importLog/'.$uuid[0].'/csv');
        $I->seeResponseCodeIs(200);
        $I->see("discovered,created,updated,malformed\n10,0,0,0");
    }
}
