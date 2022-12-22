<?php

namespace functional;

use FunctionalTester;

class ProductListControllerCest
{
    /**
     * @covers \App\Features\Products\Controllers\ProductsListController::__construct
     * @covers \App\Features\Products\Controllers\ProductsListController::productsPage
     */
    public function productsPageTest(FunctionalTester $I)
    {
        $I->amOnPage('/products');
        $I->makeHtmlSnapshot();
        $I->seeResponseCodeIs(200);
        $I->seeElement('table#productList');
        $I->seeElement('select#productCategoryFilter');
    }

    /**
     * @covers \App\Features\Products\Controllers\ProductsListController::productsListData
     */
    public function productsListDataTest(FunctionalTester $I)
    {
        $I->amOnPage('/products');
        $I->sendAjaxGetRequest('/products/datatable', []);
        $I->seeResponseCodeIs(200);
        $I->sendAjaxGetRequest('/products/datatable', [
            'columns' => [['data' => 'name'], ['data' => 'category', 'search' => ['value' => 100]]],
            'search' => ['value' => 'test'],
            'order' => [['column' => 0]],
        ]);
        $I->seeResponseCodeIs(200);
        $I->sendAjaxGetRequest('/products/datatable', [
            'columns' => [['data' => 'xxx']],
            'order' => [['column' => 0]]
        ]);
        $I->seeResponseCodeIs(400);
    }
}
