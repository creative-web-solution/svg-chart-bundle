<?php
namespace Cws\Bundle\SVGChartBundle\SVGChart\Tools;

class Line
{
    /**
     * Start a SVG line
     *
     * @param Point $point
     *
     * @return string
     */
    public static function lineFrom(Point $point)
    {
        return "M $point->x,$point->y";
    }

    /**
     * Continue a SVG line
     *
     * @param Point $point
     *
     * @return string
     */
    public static function lineTo(Point $point)
    {
        return "L $point->x,$point->y";
    }

    /**
     * Create a line between 2 points
     *
     * @param Point $point
     * @param Point $point2
     *
     * @return string
     */
    public static function lineFromTo(Point $point, Point $point2)
    {
        return "M $point->x,$point->y L $point2->x,$point2->y";
    }
}
