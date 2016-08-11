<?php
$I = new ApiTester($scenario);
$I->wantTo("list all bars near Neptune's Fountain sorted by rating");
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$I->sendGET('/api/places', ["sort" => "rating"]);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['status' => 'OK']);
