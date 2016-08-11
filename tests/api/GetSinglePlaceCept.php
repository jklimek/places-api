<?php
$I = new ApiTester($scenario);
$I->wantTo("list details about Buffet place");
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$I->sendGET('/api/places/ChIJ-XCXDXFz_UYRJKCOKbbgo1o');
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['status' => 'OK']);
$I->seeResponseContainsJson(['name' => 'Buffet']);

