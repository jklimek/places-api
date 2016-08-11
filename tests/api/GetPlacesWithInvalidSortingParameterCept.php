<?php
$I = new ApiTester($scenario);
$I->wantTo("get wrong sorting field error");
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$I->sendGET('/api/places', ["sort" => "name,rrrrating"]);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['status' => 'ERROR']);
$I->seeResponseContainsJson(['error_message' => "Invalid sorting field(s): 'rrrrating'"]);