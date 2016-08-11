<?php

use AppBundle\Service\HelpersService;

class SorterTest extends \Codeception\Test\Unit {
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var HelpersService
     */
    protected $service;
    protected $testDataset = [
        [
            "name"     => "bbb",
            "rating"   => 23,
            "distance" => 666
        ],
        [
            "name"     => "aaa",
            "rating"   => 10,
            "distance" => 100
        ],
        [
            "name"     => "ccc",
            "rating"   => 2,
            "distance" => 666
        ],
        [
            "name"     => "ddd",
            "rating"   => 0,
            "distance" => 16
        ],
    ];

    protected function _before() {
        $this->service = new HelpersService();
    }

    // tests
    public function testSorter() {
        $sortingOrder = "-name";
        $actualResult = [
            [
                "name"     => "ddd",
                "rating"   => 0,
                "distance" => 16
            ],
            [
                "name"     => "ccc",
                "rating"   => 2,
                "distance" => 666
            ],
            [
                "name"     => "bbb",
                "rating"   => 23,
                "distance" => 666
            ],
            [
                "name"     => "aaa",
                "rating"   => 10,
                "distance" => 100
            ],
        ];

        $arrayToTest = $this->service->sortArrayByFields($this->testDataset, $sortingOrder);
        $this->assertEquals($actualResult, $arrayToTest);

        $sortingOrder = "name";
        $actualResult = [

            [
                "name"     => "aaa",
                "rating"   => 10,
                "distance" => 100
            ],
            [
                "name"     => "bbb",
                "rating"   => 23,
                "distance" => 666
            ],

            [
                "name"     => "ccc",
                "rating"   => 2,
                "distance" => 666
            ],
            [
                "name"     => "ddd",
                "rating"   => 0,
                "distance" => 16
            ],

        ];

        $arrayToTest = $this->service->sortArrayByFields($this->testDataset, $sortingOrder);
        $this->assertEquals($actualResult, $arrayToTest);

        $sortingOrder = "name,-rating";
        $actualResult = [

            [
                "name"     => "aaa",
                "rating"   => 10,
                "distance" => 100
            ],
            [
                "name"     => "bbb",
                "rating"   => 23,
                "distance" => 666
            ],

            [
                "name"     => "ccc",
                "rating"   => 2,
                "distance" => 666
            ],
            [
                "name"     => "ddd",
                "rating"   => 0,
                "distance" => 16
            ],

        ];

        $arrayToTest = $this->service->sortArrayByFields($this->testDataset, $sortingOrder);
        $this->assertEquals($actualResult, $arrayToTest);


        $sortingOrder = "-distance,name";
        $actualResult = [
            [
                "name"     => "bbb",
                "rating"   => 23,
                "distance" => 666
            ],

            [
                "name"     => "ccc",
                "rating"   => 2,
                "distance" => 666
            ],
            [
                "name"     => "aaa",
                "rating"   => 10,
                "distance" => 100
            ],

            [
                "name"     => "ddd",
                "rating"   => 0,
                "distance" => 16
            ],

        ];

        $arrayToTest = $this->service->sortArrayByFields($this->testDataset, $sortingOrder);
        $this->assertEquals($actualResult, $arrayToTest);


        $sortingOrder = "-distance,name,rating";
        $actualResult = [
            [
                "name"     => "bbb",
                "rating"   => 23,
                "distance" => 666
            ],

            [
                "name"     => "ccc",
                "rating"   => 2,
                "distance" => 666
            ],
            [
                "name"     => "aaa",
                "rating"   => 10,
                "distance" => 100
            ],

            [
                "name"     => "ddd",
                "rating"   => 0,
                "distance" => 16
            ],

        ];


        $arrayToTest = $this->service->sortArrayByFields($this->testDataset, $sortingOrder);
        $this->assertEquals($actualResult, $arrayToTest);





    }
}