<?php

namespace Cws\Bundle\SVGChartBundle\SVGChart\Pie;

use Cws\Bundle\SVGChartBundle\SVGChart\Tools\Point;
use Cws\Bundle\SVGChartBundle\SVGChart\Tools\Line;
use Cws\Bundle\SVGChartBundle\SVGChart\Tools\Arc;
use SVG\Nodes\Shapes\SVGPolyline;
use SVG\Nodes\Shapes\SVGPath;

class PieSlice
{
    public $id;
    private $data;
    private $style;
    private $gfxData;

    /**
     * Pie slice constructor.
     *
     * @param object $data
     * @param object $style
     */
    public function __construct($data, $style)
    {
        $this->data    = $data->data;
        $this->id      = $this->data->id;
        $this->style   = $style;
        $this->mode    = $style->mode;
        $this->gfxData = $this->computeGfxData($data, $style);
    }

    /**
     * Get all data to help construct slices
     *
     * @param object $data
     * @param object $style
     *
     * @return object
     */
    private function computeGfxData($data, $style)
    {
        $startAngle  = $data->startAngle;
        $endAngle    = $data->endAngle;
        $centerAngle = $startAngle + ($endAngle - $startAngle) / 2;
        $center      = new Point($style->center->x, $style->center->y);

        $gfxData = (object) array(
            'data'         => $data->data,
            'radius'       => $style->radius,
            'startAngle'   => $startAngle,
            'endAngle'     => $endAngle,
            'center'       => $center,
            'centerAngle'  => $centerAngle,
            'largeArcFlag' => abs($endAngle - $startAngle) > 180 ? '1' : '0'
        );

        if ($this->mode == PIE::DONUT_MODE) {
            $internalArcRadius  = $style->radius - $style->donutThickness;
            $centerArcPoint     = Point::angleToPoint(
                $center,
                $style->radius - $style->donutThickness / 2,
                $centerAngle
            );
        } else {
            $internalArcRadius  = $style->radius;
            $centerArcPoint     = Point::angleToPoint(
                $center,
                $style->radius * $style->legend->legendLineOffset,
                $centerAngle
            );
        }

        $gfxData2 = (object) array(
            'internalRadius'   => $internalArcRadius,
            'isOnRight'        => $centerArcPoint->x > $center->x,
            'legendLinePoints' => (object) array(
                'point1' => $centerArcPoint,
                'point2' => null,
                'point3' => null
            ),
            'internalStartPoint' => Point::angleToPoint($center, $internalArcRadius, $startAngle),
            'internalEndPoint'   => Point::angleToPoint($center, $internalArcRadius, $endAngle),
            'externalStartPoint' => Point::angleToPoint($center, $style->radius, $startAngle),
            'externalEndPoint'   => Point::angleToPoint($center, $style->radius, $endAngle)
        );

        return (object) array_merge((array) $gfxData, (array) $gfxData2);
    }

    /**
     * Update the points of the legend line
     *
     * @param Point $point2
     * @param Point $point3
     */
    public function setLegendLinesPoints(Point $point2, Point $point3)
    {
        $this->gfxData->legendLinePoints->point2 = $point2;
        $this->gfxData->legendLinePoints->point3 = $point3;
    }

    /**
     * Return the complete SVG string of a donut slice
     *
     * @return string
     */
    private function makeSVGDonutSliceString()
    {
        $internalArc = Arc::arcFromTo(
            $this->gfxData->internalStartPoint,
            $this->gfxData->internalEndPoint,
            $this->gfxData->internalRadius,
            0,
            $this->gfxData->largeArcFlag,
            1
        );

        $line1 = Line::lineTo($this->gfxData->externalEndPoint);

        $externalArc = Arc::arcTo(
            $this->gfxData->externalStartPoint,
            $this->gfxData->radius,
            0,
            $this->gfxData->largeArcFlag,
            0
        );

        $line2 = Line::lineTo($this->gfxData->internalStartPoint);

        return implode(' ', array($internalArc, $line1, $externalArc, $line2));
    }

    /**
     * Return the complete SVG string of a pie slice
     *
     * @return string
     */
    private function makeSVGPieSliceString()
    {
        $line1 = Line::lineFromTo($this->gfxData->center, $this->gfxData->externalEndPoint);

        $arc = Arc::arcTo(
            $this->gfxData->externalStartPoint,
            $this->gfxData->radius,
            0,
            $this->gfxData->largeArcFlag,
            0
        );

        $line2 = Line::lineTo($this->gfxData->center);

        return implode(' ', array($line1, $arc, $line2));
    }

    /**
     * Create and return the SVGPath of one slice
     *
     * @return SVGPath
     */
    public function create()
    {
        if ($this->mode == PIE::DONUT_MODE) {
            $svgPath = new SVGPath($this->makeSVGDonutSliceString());
        } else {
            $svgPath = new SVGPath($this->makeSVGPieSliceString());
        }

        $svgPath->setAttribute('fill', $this->data->color);

        if (isset($this->data->id)) {
            $svgPath->setAttribute('data-id', $this->data->id);
        }

        if (isset($this->style->cssSliceClass)) {
            $svgPath->setAttribute('class', $this->style->cssSliceClass);
        }

        return $svgPath;
    }

    /**
     * Create the line of the legend (that go from inside to the outside of the chart)
     *
     * @return SVGLine
     */
    public function getLegendLine()
    {
        $line = new SVGPolyline(
            array(
                $this->gfxData->legendLinePoints->point1->toArray(),
                $this->gfxData->legendLinePoints->point2->toArray(),
                $this->gfxData->legendLinePoints->point3->toArray()
            )
        );

        $line->setAttribute('stroke', $this->style->legend->lineColor);
        $line->setAttribute('fill', 'none');

        if (isset($this->data->id)) {
            $line->setAttribute('data-id', $this->data->id);
        }

        if (isset($this->style->legend->cssLegendLineClass)) {
            $line->setAttribute('class', $this->style->legend->cssLegendLineClass);
        }

        return $line;
    }

    /**
     * Get the graphics data of the slice
     *
     * @return object
     */
    public function getGfxData()
    {
        return $this->gfxData;
    }
}
