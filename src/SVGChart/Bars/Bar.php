<?php

namespace Cws\Bundle\SVGChartBundle\SVGChart\Bars;

use Cws\Bundle\SVGChartBundle\SVGChart\Tools\Point;
use Cws\Bundle\SVGChartBundle\SVGChart\Tools\Line;
use Cws\Bundle\SVGChartBundle\SVGChart\Tools\Arc;
use SVG\Nodes\Shapes\SVGPolyline;
use SVG\Nodes\Shapes\SVGPath;

class Bar
{
    private $style;
    public $gfxData;

    /**
     * Bar constructor.
     *
     * @param array $gfxData
     * @param object $style
     */
    public function __construct($gfxData, $style)
    {
        $this->gfxData = $gfxData;
        $this->style   = $style;
    }

    /**
     * Create and return the SVGPath of one bar
     *
     * @return SVGPath
     */
    public function create()
    {
        $svgBar = array();
        $svgBar[] = Line::lineFrom($this->gfxData->p1);
        $svgBar[] = Line::lineTo($this->gfxData->p2);
        $svgBar[] = Line::lineTo($this->gfxData->p3);
        $svgBar[] = Line::lineTo($this->gfxData->p4);
        $svgBar[] = Line::lineTo($this->gfxData->p1);

        $svgPath = implode(' ', $svgBar);

        $bar = new SVGPath($svgPath);
        $bar->setAttribute('stroke', $this->gfxData->stroke);
        $bar->setAttribute('stroke-width', $this->style->thickness);
        $bar->setAttribute('stroke-linecap', $this->style->linecap);
        $bar->setAttribute('stroke-linejoin', $this->style->linejoin);
        $bar->setAttribute('fill', $this->gfxData->color);

        if (isset($this->gfxData->id)) {
            $bar->setAttribute('data-id', $this->gfxData->id);
        }

        if (isset($this->style->cssBarClass)) {
            $bar->setAttribute('class', $this->style->cssBarClass);
        }

        return $bar;
    }
}
