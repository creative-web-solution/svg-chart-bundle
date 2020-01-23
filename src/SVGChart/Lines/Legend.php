<?php

namespace Cws\Bundle\SVGChartBundle\SVGChart\Lines;

use Cws\Bundle\SVGChartBundle\SVGChart\IChartLegend;
use Cws\Bundle\SVGChartBundle\SVGChart\Tools\Point;
use Cws\Bundle\SVGChartBundle\SVGChart\Tools\Text;

class Legend implements IChartLegend
{
    private $style;
    private $globalGfxData;
    private $labelList;

    /**
     * Lines chart legend constructor.
     *
     * @param Lines $linesChart
     * @param object $style
     */
    public function __construct($linesChart, $style)
    {
        $this->style         = $style;
        $this->globalGfxData = $linesChart->getGlobalGfxData();
        $this->labelList     = array();
    }

    /**
     * Create all the abscissal labels
     *
     * @return string
     */
    private function createHorizontalLabels()
    {
        $html = array();

        $html[] = '<div class="'.$this->style->axes->abs->wrapperCssClass.'">';

        foreach ($this->globalGfxData->ordMinMax as $index => $ordData) {
            $point1 = new Point(
                $this->globalGfxData->absSteps[ $index ],
                $this->globalGfxData->bottom
            );

            $html[] = $this->createLabels(
                $this->style->axes->abs->labels[ $index ]->label,
                $point1,
                $this->style->axes->abs->labelCssClass
            );
        }

        $html[] = '</div>';

        return implode('', $html);
    }

    /**
     * Create all the ordinate labels
     *
     * @return string
     */
    private function createVerticalLabels()
    {
        $html   = array();
        $min    = $this->style->axes->ord->min;
        $max    = $this->style->axes->ord->max;
        $delta  = $max - $min;
        $step   = $this->style->axes->ord->step;

        $html[] = '<div class="'.$this->style->axes->ord->wrapperCssClass.'">';


        for ($linePos = 0; $linePos <= $delta; $linePos = $linePos + $step) {
            $point1 = new Point(
                $this->globalGfxData->left,
                $this->globalGfxData->bottom - round(
                    $linePos * $this->globalGfxData->height /
                    $delta,
                    3
                )
            );

            $html[] = $this->createLabels(
                $linePos + $this->style->axes->ord->min,
                $point1,
                $this->style->axes->ord->labelCssClass
            );
        }

        $html[] = '</div>';

        return implode('', $html);
    }

    /**
     * Create all labels
     *
     * @param Point $point1
     * @param string $label
     * @param string $cssClass
     *
     * @return SVGText
     */
    private function createLabels($label, Point $point1, $cssClass)
    {
        $text = new Text($label, $point1, $cssClass);

        return $text->create();
    }


    /**
     * Create the HTML of all labels
     *
     * @return string
     */
    public function create()
    {
        $html = $this->createHorizontalLabels();
        $html .= $this->createVerticalLabels();

        return $html;
    }
}
