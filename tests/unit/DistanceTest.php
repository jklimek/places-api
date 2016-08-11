<?php

use AppBundle\Service\HelpersService;

class DistanceTest extends \Codeception\Test\Unit {
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var HelpersService
     */
    protected $service;

    protected function _before() {
        $this->service = new HelpersService();
    }

    // tests
    public function testSorter() {

        $location = [
            "from" => "54.363049,18.648187",
            "to" => "54.348538,18.653228",
            "distance" => 1646
        ];
        $distanceToTest = $this->service->calculateDistanceFromLocation($location["from"], $location["to"]);
        $this->assertEquals($location["distance"], $distanceToTest);


        $location = [
            "from" => "54.352743,18.6575306",
            "to" => "-54.348538,18.653228",
            "distance" => 12087031
        ];
        $distanceToTest = $this->service->calculateDistanceFromLocation($location["from"], $location["to"]);
        $this->assertEquals($location["distance"], $distanceToTest);


    }
}