<?php

namespace Cws\Bundle\SVGChartBundle\SVGChart\Bars;

use SVG\Nodes\Structures\SVGDocumentFragment;
use SVG\Nodes\Shapes\SVGLine;
use Cws\Bundle\SVGChartBundle\SVGChart\Tools\Point;

class Axes
{
    private $style;
    private $labelList;

    /**
     * Bars chart axes constructor.
     *
     * @param object $style
     */
    public function __construct($style)
    {
        $this->style     = $style;
        $this->labelList = array();
    }

    /**
     * Create a line between 2 points
     *
     * @param Point $point1
     * @param Point $point2
     * @param string $color
     * @param int $thickness
     *
     * @return SVGLine
     */
    private function getLine(Point $point1, Point $point2, $color, $thickness)
    {
        $line = new SVGLine($point1->x, $point1->y, $point2->x, $point2->y);
        $line->setStyle('stroke', $color);
        $line->setStyle('stroke-width', $thickness);
        $line->setStyle('fill', 'none');

        return $line;
    }

    /**
     * Return the abscissal line and append it to the SVG document
     *
     * @return SVGLine
     */
    private function createAbs()
    {
        $point1 = new Point(
            $this->style->canvas->left,
            $this->style->canvas->top + $this->style->canvas->height
        );

        $point2 = new Point($point1->x + $this->style->canvas->width, $point1->y);

        return $this->getLine(
            $point1,
            $point2,
            $this->style->axes->abs->color,
            $this->style->axes->abs->thickness
        );
    }

    /**
     * Return the ordinate line and append it to the SVG document
     *
     * @return SVGLine
     */
    private function createOrd()
    {
        $point1 = new Point(
            $this->style->canvas->left,
            $this->style->canvas->top - $this->style->axes->ord->marginY
        );

        $point2 = new Point(
            $point1->x,
            $this->style->canvas->top + $this->style->canvas->height
        );

        return $this->getLine(
            $point1,
            $point2,
            $this->style->axes->ord->color,
            $this->style->axes->ord->thickness
        );
    }

    /**
     * Create all the horizontal lines of the grid and append it to the SVG document
     *
     * @param SVGDocumentFragment $svgDocument
     */
    private function createHorizontalGridLines(SVGDocumentFragment $svgDocument)
    {
        $min   = $this->style->axes->ord->min;
        $max   = $this->style->axes->ord->max;
        $step  = $this->style->axes->ord->step;
        $delta = $max - $min;

        for ($linePos = $step; $linePos <= $delta; $linePos = $linePos + $step) {
            $point1 = new Point(
                $this->style->canvas->left,
                $this->style->canvas->top + $this->style->canvas->height - round(
                    $linePos * $this->style->canvas->height / $delta,
                    3
                )
            );

            $point2 = new Point($this->style->canvas->left + $this->style->canvas->width, $point1->y);

            $svgDocument->addChild(
                $this->getLine(
                    $point1,
                    $point2,
                    $this->style->grid->horizontal->color,
                    $this->style->grid->horizontal->thickness
                )
            );
        }
    }


    /**
     * Create and add to the SVG document all background lines and grids
     *
     * @param SVGDocumentFragment $svgDocument
     *
     * @return string
     */
    public function create(SVGDocumentFragment $svgDocument)
    {
        if ($this->style->grid->horizontal->isDisplayed) {
            $this->createHorizontalGridLines($svgDocument);
        }
        if ($this->style->axes->abs->isDisplayed) {
            $svgDocument->addChild($this->createAbs());
        }
        if ($this->style->axes->ord->isDisplayed) {
            $svgDocument->addChild($this->createOrd());
        }
    }
}
