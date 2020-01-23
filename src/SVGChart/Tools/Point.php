<?php
namespace Cws\Bundle\SVGChartBundle\SVGChart\Tools;

class Point
{
    public $x;
    public $y;

    /**
     * Create a point
     */
    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Convert a point to an array
     *
     * @return array
     */
    public function toArray()
    {
        return array($this->x, $this->y);
    }

    /**
     * Rotate a point relatively to a center and an angle
     *
     * @param Point $center
     * @param int $radius
     * @param int $angle
     *
     * @return Point
     */
    public static function angleToPoint(Point $center, $radius, $angle)
    {
        return new Point(
            round($center->x + $radius * cos(deg2rad($angle)), 3),
            round($center->y + $radius * sin(deg2rad($angle)), 3)
        );
    }
}
