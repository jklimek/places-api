<?php
$I = new ApiTester($scenario);
$I->wantTo("list 2 pages of bars near Neptune's Fountain");
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$I->sendGET('/api/places');
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['status' => 'OK']);
$nextPageUri = $I->grabDataFromResponseByJsonPath('$.next_page');
sleep(3); // Weird Google latency between requests with next_page_token
$I->sendGET(trim($nextPageUri[0]));
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['status' => 'OK']);
