<?php
/**
 * Created by PhpStorm.
 * User: klemens
 * Date: 08/08/16
 * Time: 22:28
 */

namespace AppBundle;


use AppBundle\Service\Helpers;

class PlacesManager {

    private $helpersService;


    public function __construct(Helpers $helpersService) {

        $this->helpersService = $helpersService;
    }
}