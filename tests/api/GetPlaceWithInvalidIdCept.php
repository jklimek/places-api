<?php
$I = new ApiTester($scenario);
$I->wantTo("get invalid place details request error");
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$I->sendGET('/api/places/badId');
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['status' => 'ERROR']);
$I->seeResponseContainsJson(['error_message' => 'INVALID_REQUEST']);
