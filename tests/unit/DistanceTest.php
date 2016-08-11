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

        $location1 = [
            "from" => "54.363049,18.648187",
            "to" => "54.348538,18.653228",
            "distance" => 1646
        ];
        $distanceToTest1 = $this->service->calculateDistanceFromLocation($location1["from"], $location1["to"]);
        $this->assertEquals($location1["distance"], $distanceToTest1);


        $location1 = [
            "from" => "54.352743,18.6575306",
            "to" => "-54.348538,18.653228",
            "distance" => 12087031
        ];
        $distanceToTest1 = $this->service->calculateDistanceFromLocation($location1["from"], $location1["to"]);
        $this->assertEquals($location1["distance"], $distanceToTest1);


    }
}