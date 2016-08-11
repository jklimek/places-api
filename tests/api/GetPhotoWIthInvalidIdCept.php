<?php 
$I = new ApiTester($scenario);
$I->wantTo("get bad photo request error");
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$I->sendGET('/api/photos/badPhotoId');
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['status' => 'ERROR']);
$I->seeResponseContainsJson(['error_message' => "Bad request"]);
