<?php

namespace CwsBundle\SVGChart\Pie;

use CwsBundle\SVGChart\ISVGChart;
use CwsBundle\SVGChart\Tools\Point;
use SVG\Nodes\Structures\SVGDocumentFragment;

class Pie implements ISVGChart
{
    const TOTAL_ANGLE   = 360;
    const MIN_ARC_ANGLE = 10;
    const PIE_MODE      = 'pie';
    const DONUT_MODE    = 'donut';

    private $data;
    private $style;
    private $gfxData;
    private $hasLegend;

    /**
     * Pie constructor.
     *
     * @param object $data
     * @param object $style
     */
    public function __construct($data, $style)
    {
        $this->data      = $data[0]->values;
        $this->style     = $style;
        $this->hasLegend = isset($this->style->legend);
        $this->gfxData   = $this->createAllSlices($this->data, $this->style);
    }

    /**
     * Sort the slice depending on their y position
     *
     * @param PieSlice $a
     * @param PieSlice $b
     *
     * @return int
     */
    private function sortSlice($a, $b)
    {
        if ($a->y == $b->y) {
            return 0;
        }
        return ($a->y < $b->y) ? -1 : 1;
    }

    /**
     * Create all slices
     *
     * @param object $data
     * @param object $style
     *
     * @return object
     */
    private function createAllSlices($data, $style)
    {
        $lastAngle            = $style->angleOffset;
        $count                = count($data);
        $total                = 0;
        $sliceList            = array();
        $sortedLeftSliceList  = array();
        $sortedRightSliceList = array();

        foreach ($data as $value) {
            $total = $total + $value->value;
        };

        foreach ($data as $index => $value) {
            if ($index < $count - 1) {
                $nextAngle = $lastAngle + $value->value * Pie::TOTAL_ANGLE / $total;
            } else {
                $nextAngle = Pie::TOTAL_ANGLE + $style->angleOffset;
            }

            $nextAngle = max($nextAngle, Pie::MIN_ARC_ANGLE);

            $sliceData = (object) array(
                'id'         => isset($value->id) ? $value->id : null,
                'data'       => $value,
                'total'      => $total,
                'startAngle' => $lastAngle,
                'endAngle'   => $nextAngle
            );

            $pieSliceData  = new PieSlice($sliceData, $style);
            $pieSliceGfxData = $pieSliceData->getGfxData();

            // Used for SVG construction. Must be in the same order as original data
            $sliceList[] = $pieSliceData;

            if ($this->hasLegend) {
                // Used for label construction. Must be ordered depending of the y position
                if ($pieSliceGfxData->isOnRight) {
                    $sortedRightSliceList[] = (object)array(
                        'id'    => $index,
                        'y'     => $pieSliceGfxData->legendLinePoints->point1->y,
                        'slice' => $pieSliceData
                    );
                } else {
                    $sortedLeftSliceList[] = (object)array(
                        'id'    => $index,
                        'y'     => $pieSliceGfxData->legendLinePoints->point1->y,
                        'slice' => $pieSliceData
                    );
                }
            }

            $lastAngle = $nextAngle;
        }

        if ($this->hasLegend) {
            $height = $this->style->height;

            $this->createLegendLinePoints(
                $sortedRightSliceList,
                $height,
                $this->style->width - $this->style->legend->textMaxWidth,
                $this->style->width
            );

            $this->createLegendLinePoints(
                $sortedLeftSliceList,
                $height,
                $this->style->legend->textMaxWidth,
                0
            );
        }

        return (object) array(
            'sliceList' => $sliceList,
            'center'    => new Point($style->center->x, $style->center->y)
        );
    }

    /**
     * Create the line that go from the middle of slice to the exterior of the pie
     *
     * @param PieSlice[] $array
     * @param int $height
     * @param int $x1
     * @param int $x2
     */
    private function createLegendLinePoints(array &$array, $height, $x1, $x2)
    {
        usort($array, array($this, 'sortSlice'));
        $count = count($array);
        $step = $count > 1 ? round($height / ($count + 1)) : round($height / 2);

        foreach ($array as $index => $value) {
            $legendY = $step + $step * $index + $this->style->legend->textHeight / 2;
            $value->slice->setLegendLinesPoints(
                new Point($x1, $legendY),
                new Point($x2, $legendY)
            );
        }
    }

    /**
     * Create the pie/donut and its axes and append them to the SVG document
     *
     * @param SVGDocumentFragment $svgDocument
     *
     * @return string
     */
    public function create(SVGDocumentFragment $svgDocument)
    {
        foreach ($this->gfxData->sliceList as $slice) {
            $svgDocument->addChild($slice->create());
        }

        if ($this->hasLegend) {
            foreach ($this->gfxData->sliceList as $slice) {
                $svgDocument->addChild($slice->getLegendLine());
            }
        }
    }

    /**
     * Create and return the legend if it exists
     *
     * @return string
     */
    public function getLegend()
    {
        if ($this->hasLegend) {
            $legend = new Legend($this, $this->style);

            return $legend->create();
        }

        return '';
    }

    /**
     * Return computed graphics data of the pie
     */
    public function getGfxData()
    {
        return $this->gfxData;
    }
}
