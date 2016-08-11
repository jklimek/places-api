<?php
$I = new ApiTester($scenario);
$I->wantTo("list details about Buffet place");
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$I->sendGET('/api/places/ChIJ-XCXDXFz_UYRJKCOKbbgo1o', ["key" => "AIzaSyA3yvqq0jKmnYk8-EX_DioJhSH2ubgfmZs"]);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['status' => 'OK']);
$I->seeResponseContainsJson(['name' => 'Buffet']);

