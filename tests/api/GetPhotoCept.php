<?php
$I = new ApiTester($scenario);
$I->wantTo("see single photo");
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$I->sendGET('/api/photos/CoQBcwAAAPfuLuS3L_F2iSqzF92TErCeb9OyaVXRc96KU94JHkS0wwvveBS7o9PB9Kv5AUm9m8wlvP2X3w1LtnbHMgasSZyMNjWAf53nzMS0fcyo60XbWS2qE6vD2hAicBHDYO1TUE9Igtjr_dHL19sHLIqM6xms3l-GZ9RpyYVIb0PwlUvQEhCha193fEWFhITpVS0vVyXvGhQomqzwRYtLES_FpktASNoQKfQhgg');
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); // 200
$I->seeHttpHeader('Content-Type','image/png');
