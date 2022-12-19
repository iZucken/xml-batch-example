<?php

namespace functional;

use FunctionalTester;

class DefaultControllerCest
{
    /**
     * @covers \App\Controller\DefaultController::defaultAction
     */
    public function defaultActionTest(FunctionalTester $I)
    {
        $I->amOnPage('/');
        $I->see('PIM test task', 'h2');
    }
}
