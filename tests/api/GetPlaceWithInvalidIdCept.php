<?php
$I = new ApiTester($scenario);
$I->wantTo("get invalid place details request error");
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$I->sendGET('/api/places/badId', ["key" => "AIzaSyA3yvqq0jKmnYk8-EX_DioJhSH2ubgfmZs"]);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['status' => 'ERROR']);
$I->seeResponseContainsJson(['error_message' => 'INVALID_REQUEST']);
