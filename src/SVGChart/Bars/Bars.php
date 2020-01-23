<?php

namespace Cws\Bundle\SVGChartBundle\SVGChart\Bars;

use Cws\Bundle\SVGChartBundle\SVGChart\ISVGChart;
use Cws\Bundle\SVGChartBundle\SVGChart\Tools\Point;
use SVG\Nodes\Structures\SVGDocumentFragment;

class Bars implements ISVGChart
{
    private $data;
    private $style;
    private $hasLegend;
    private $barList;

    /**
     * Bars chart constructor.
     *
     * @param object $data
     * @param object $style
     */
    public function __construct($data, $style)
    {
        $this->data    = $data[0]->values;
        $this->style   = $style;
        $this->barList = $this->createBars($this->data, $this->style);
    }

    /**
     * Compute the data to help create bars
     *
     * @param object $data
     * @param object $style
     *
     * @return array
     */
    private function getBarsGfxData($data, $style)
    {
        $barGfxData = array();
        $barsCount  = count($data);
        $barWidth   = null;
        $delta      = $style->axes->ord->max - $style->axes->ord->min;

        if (isset($style->axes->abs->barWidth)) {
            $barWidth = $style->axes->abs->barWidth;
        } else {
            $barWidth = round(
                (
                    $style->canvas->width -
                    $style->canvas->marginX * 2 -
                    $style->axes->abs->barGap * ($barsCount - 1)
                ) /
                $barsCount
            );
        }

        foreach ($data as $index => $currentBarData) {
            $x1 = $style->canvas->left + $style->canvas->marginX +
                ($style->axes->abs->barGap + $barWidth) * $index;
            $x2 = $x1 + $barWidth;
            $y2 = $style->canvas->top + $style->canvas->height;
            $y1 = $y2 - round(
                ($currentBarData->value - $style->axes->ord->min) * $style->canvas->height / $delta,
                3
            );

            $barGfxData[] = (object) array(
                'p1'      => new Point($x1, $y1),
                'p2'      => new Point($x2, $y1),
                'p3'      => new Point($x2, $y2),
                'p4'      => new Point($x1, $y2),
                'pLegend' => new Point($x1 + $barWidth / 2, $y2),
                'color'   => $currentBarData->color,
                'stroke'  => $currentBarData->stroke,
                'width'   => $barWidth,
                'id'      => $currentBarData->id
            );
        }

        return $barGfxData;
    }

    /**
     * Create all bars
     *
     * @param object $data
     * @param object $style
     *
     * @return array
     */
    private function createBars($data, $style)
    {
        $barList = array();

        $barsGfxData = $this->getBarsGfxData($data, $style);

        foreach ($barsGfxData as $index => $gfxData) {
            $barList[] = new Bar($gfxData, $style);
        }

        return $barList;
    }

    /**
     * Create bars and axes, and add them to the SVG document
     *
     * @param SVGDocumentFragment $svgDocument
     */
    public function create(SVGDocumentFragment $svgDocument)
    {
        $axes = new Axes($this->style);
        $axes->create($svgDocument);

        foreach ($this->barList as $bar) {
            $svgDocument->addChild($bar->create());
        }
    }

    /**
     * Create and return the legend
     *
     * @return string
     */
    public function getLegend()
    {
        $legend = new Legend($this, $this->style);

        return $legend->create();
    }

    /**
     * Return the list of all bars
     *
     * @return array
     */
    public function getGfxData()
    {
        return $this->barList;
    }
}
