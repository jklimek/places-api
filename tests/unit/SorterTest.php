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
    protected $array1 = [
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
        $sortingOrder1 = "-name";
        $arrayToCompare1 = [
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

        $sortingOrder2 = "name";
        $arrayToCompare2 = [

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

        $sortingOrder3 = "name,-rating";
        $arrayToCompare3 = [

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

        $sortingOrder4 = "-distance,name";
        $arrayToCompare4 = [
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

        $sortingOrder5 = "-distance,name,rating";
        $arrayToCompare5 = [
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



        $arrayToTest1 = $this->service->sortArrayByFields($this->array1, $sortingOrder1);
        $this->assertEquals($arrayToCompare1, $arrayToTest1);

        $arrayToTest2 = $this->service->sortArrayByFields($this->array1, $sortingOrder2);
        $this->assertEquals($arrayToCompare2, $arrayToTest2);

        $arrayToTest3 = $this->service->sortArrayByFields($this->array1, $sortingOrder3);
        $this->assertEquals($arrayToCompare3, $arrayToTest3);

        $arrayToTest4 = $this->service->sortArrayByFields($this->array1, $sortingOrder4);
        $this->assertEquals($arrayToCompare4, $arrayToTest4);

        $arrayToTest5 = $this->service->sortArrayByFields($this->array1, $sortingOrder5);
        $this->assertEquals($arrayToCompare5, $arrayToTest5);


    }
}