<?php
$I = new ApiTester($scenario);
$I->wantTo("get wrong resource error");
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$I->sendGET('/api/placesssss', ["key" => "AIzaSyA3yvqq0jKmnYk8-EX_DioJhSH2ubgfmZs"]);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['status' => 'ERROR']);