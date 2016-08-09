<?php
/**
 * Created by PhpStorm.
 * User: klemens
 * Date: 08/08/16
 * Time: 16:40
 */

namespace AppBundle\Service;


class Helpers {

    /**
     * Function to calculate distance between two points (latitude, longitude)
     * @param $startingPoint
     * @param $destinationPoint
     * @return int Meters from $startingPoint to $destinationPoint
     */
    public function calculateDistanceFromLocation($startingPoint, $destinationPoint) {

        list($latitudeFrom, $longitudeFrom) = explode(",", $startingPoint);
        list($latitudeTo, $longitudeTo) = explode(",", $destinationPoint);
        $earthRadius = 6371000;

        // Thanks to martinstoeckli http://stackoverflow.com/a/10054282
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        return round($angle * $earthRadius);

    }

    /**
     * Closure generator used to sort arrays by defined order ($sortingOrder)
     * @param array $sortingOrder Array containing sorting order (e.g. ["-rating", "distance"]
     * @return \Closure Closuer used for sorting
     */
    public function sorter($sortingOrder) {
        // Return closure (cannot use class method along with use() construct in usort)
        return function($elementA, $elementB) use ($sortingOrder) {
            // Keep
            while(!empty($sortingOrder)) {
                $sortBy = array_shift($sortingOrder);
                $order = 1;
                if (substr($sortBy, 0, 1) == "-") {
                    $order = -$order;
                    $sortBy = substr($sortBy, 1);
                }
                if ($elementA[$sortBy] > $elementB[$sortBy]) {
                    return $order;
                } elseif ($elementA[$sortBy] < $elementB[$sortBy]) {
                    return -$order;
                }
            }
            return true;
        };
    }
}