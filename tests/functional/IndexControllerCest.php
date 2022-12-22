<?php

namespace functional;

use FunctionalTester;

class IndexControllerCest
{
    /**
     * @covers \App\Features\DashboardIndex\Controllers\IndexController::indexPage
     */
    public function defaultActionTest(FunctionalTester $I)
    {
        $I->amOnPage('/');
        $I->see('Тестовое задание', 'h1');
    }
}
