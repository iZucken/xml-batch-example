<?php

namespace functional;

use FunctionalTester;

class ProductListControllerCest
{
    /**
     * @covers \App\Controller\ProductsListController::productsPage
     */
    public function productsPageTest(FunctionalTester $I)
    {
        $I->amOnPage('/products');
        $I->seeElement('table#productList');
        $I->seeElement('select#productCategoryFilter');
    }

    /**
     * @covers \App\Controller\ProductsListController::productsListData
     */
    public function productsListDataTest(FunctionalTester $I)
    {
        $I->amOnPage('/products');
        $I->sendAjaxGetRequest('/products/datatable', []);
        $I->seeResponseCodeIs(200);
        $I->sendAjaxGetRequest('/products/datatable', [
            'columns' => [['data' => 'category', 'search' => ['value' => 100]]],
        ]);
        $I->seeResponseCodeIs(200);
        $I->sendAjaxGetRequest('/products/datatable', [
            'columns' => [['data' => 'id']],
            'order' => [['column' => 0]]
        ]);
        $I->seeResponseCodeIs(200);
        $I->sendAjaxGetRequest('/products/datatable', [
            'columns' => [['data' => 'name']],
            'order' => [['column' => 0]]
        ]);
        $I->seeResponseCodeIs(200);
        $I->sendAjaxGetRequest('/products/datatable', [
            'columns' => [['data' => 'weight']],
            'order' => [['column' => 0]]
        ]);
        $I->seeResponseCodeIs(200);
        $I->sendAjaxGetRequest('/products/datatable', [
            'columns' => [['data' => 'category']],
            'order' => [['column' => 0]]
        ]);
        $I->seeResponseCodeIs(200);
        $I->sendAjaxGetRequest('/products/datatable', [
            'search' => ['value' => 'test']
        ]);
        $I->seeResponseCodeIs(200);
    }
}
