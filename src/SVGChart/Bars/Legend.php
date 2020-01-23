<?php

namespace Cws\Bundle\SVGChartBundle\SVGChart\Bars;

use Cws\Bundle\SVGChartBundle\SVGChart\IChartLegend;
use Cws\Bundle\SVGChartBundle\SVGChart\Tools\Point;
use Cws\Bundle\SVGChartBundle\SVGChart\Tools\Text;

class Legend implements IChartLegend
{
    private $style;
    private $gfxData;
    private $labelList;

    /**
     * Bars chart legend constructor.
     *
     * @param Bars $barsChart
     * @param object $style
     */
    public function __construct($barsChart, $style)
    {
        $this->style     = $style;
        $this->gfxData   = $barsChart->getGfxData();
        $this->labelList = array();
    }

    /**
     * Create all the labels of abscissa
     *
     * @return string
     */
    private function createHorizontalLabels()
    {
        $html = array();

        $html[] = '<div class="'.$this->style->axes->abs->wrapperCssClass.'">';

        foreach ($this->gfxData as $index => $bar) {
            $html[] = $this->createLabels(
                $this->style->axes->abs->labels[ $index ]->label,
                $bar->gfxData->pLegend,
                $this->style->axes->abs->labelCssClass,
                '',
                false,
                $bar->gfxData->id
            );
        }

        $html[] = '</div>';

        return implode('', $html);
    }

    /**
     * Create all the labels of ordinate
     *
     * @return string
     */
    private function createVerticalLabels()
    {
        $html  = array();
        $min   = $this->style->axes->ord->min;
        $max   = $this->style->axes->ord->max;
        $step  = $this->style->axes->ord->step;
        $delta = $max - $min;

        $html[] = '<div class="'.$this->style->axes->ord->wrapperCssClass.'">';

        for ($linePos = 0; $linePos <= $delta; $linePos = $linePos + $step) {
            $point1 = new Point(
                $this->style->canvas->left,
                $this->style->canvas->top + $this->style->canvas->height - round(
                    $linePos * $this->style->canvas->height /
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
     * Create all the labels
     *
     * @param Point $point1
     * @param string $label
     * @param string $cssClass
     * @param string $color
     * @param boolean $isRightPositioned
     * @param string $id
     *
     * @return SVGText
     */
    private function createLabels($label, Point $point1, $cssClass, $color = '', $isRightPositioned = false, $id = null)
    {
        $text = new Text($label, $point1, $cssClass, $color, $isRightPositioned, $id);

        return $text->create();
    }

    /**
     * Create the html of all labels
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
