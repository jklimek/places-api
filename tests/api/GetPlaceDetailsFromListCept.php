<?php
$I = new ApiTester($scenario);
$I->wantTo("list bars and get details about first one near Neptune's Fountain");
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$I->sendGET('/api/places');
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['status' => 'OK']);
$placePageUri = $I->grabDataFromResponseByJsonPath('$.results[0].links.self.href');
$I->sendGET(trim($placePageUri[0]));
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->seeResponseJsonMatchesJsonPath('$.results.name');
