<?php
$I = new ApiTester($scenario);
$I->wantTo("get wrong parameter error");
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$I->sendGET('/api/places', ["sorttt" => "rating"]);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['status' => 'ERROR']);
$I->seeResponseContainsJson(['error_message' => "Invalid parameter(s): 'sorttt'"]);

