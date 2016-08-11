<?php
$I = new ApiTester($scenario);
$I->wantTo("list bars and get details about first one near Neptune's Fountain");
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$I->sendGET('/api/places', ["key" => "AIzaSyA3yvqq0jKmnYk8-EX_DioJhSH2ubgfmZs"]);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['status' => 'OK']);
$placePageUri = $I->grabDataFromResponseByJsonPath('$.results[0].links.self.href');
$I->sendGET(trim($placePageUri[0]), ["key" => "AIzaSyA3yvqq0jKmnYk8-EX_DioJhSH2ubgfmZs"]);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.results.name');
