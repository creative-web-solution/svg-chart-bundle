<?php
namespace CwsBundle\SVGChart\Tools;

class Arc
{
    /**
     * Start an SVG arc from a point and go to another
     *
     * @param Point $startPoint
     * @param Point $endPoint
     * @param int $radius
     * @param int $axeRotation
     * @param int $largeArcFlag
     * @param int $sweepFlag
     *
     * @return string
     */
    public static function arcFromTo(
        Point $startPoint,
        Point $endPoint,
        $radius,
        $axeRotation = 0,
        $largeArcFlag = 0,
        $sweepFlag = 0
    ) {
        return "M $startPoint->x,$startPoint->y A " .
        "$radius $radius $axeRotation $largeArcFlag $sweepFlag $endPoint->x,$endPoint->y";
    }

    /**
     * Continue an SVG arc to a point
     *
     * @param Point $endPoint
     * @param int $radius
     * @param int $axeRotation
     * @param int $largeArcFlag
     * @param int $sweepFlag
     *
     * @return string
     */
    public static function arcTo(
        Point $endPoint,
        $radius,
        $axeRotation = 0,
        $largeArcFlag = 0,
        $sweepFlag = 0
    ) {
        return "A $radius $radius $axeRotation $largeArcFlag $sweepFlag $endPoint->x,$endPoint->y";
    }
}
