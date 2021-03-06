<?php

namespace Cws\Bundle\SVGChartBundle\SVGChart\Pie;

use Cws\Bundle\SVGChartBundle\SVGChart\IChartLegend;
use Cws\Bundle\SVGChartBundle\SVGChart\Tools\Point;
use Cws\Bundle\SVGChartBundle\SVGChart\Tools\Text;

class Legend implements IChartLegend
{
    private $mode;
    private $style;
    private $gfxData;

    /**
     * Pie legend constructor.
     *
     * @param Pie $pieChart
     * @param object $style
     */
    public function __construct($pieChart, $style)
    {
        $this->style   = $style;
        $this->mode    = $style->mode;
        $this->gfxData = $pieChart->getGfxData();
    }

    /**
     * Create all the labels
     *
     * @param PieSlice[] $sliceList
     *
     * @return string
     */
    private function createAllLabels($sliceList)
    {
        $html = array();

        foreach ($sliceList as $slice) {
            $html[] = $this->createLabel($slice);
        }

        return implode('', $html);
    }


    /**
     * Create one label
     *
     * @param PieSlice $slice
     *
     * @return string
     */
    private function createLabel($slice)
    {
        $gfxData  = $slice->getGfxData();
        $cssClass = $this->style->legend->labelCssClass;

        if ($gfxData->isOnRight) {
            $cssClass .= ' alt';
        }

        if ($this->style->legend->hasLine) {
            $position = $gfxData->legendLinePoints->point2;
        }
        else {
            $position = $gfxData->legendLinePoints->point1;
        }


        $text = new Text(
            $gfxData->data->label,
            $position,
            $cssClass,
            $gfxData->data->color,
            false,
            isset($gfxData->data->id) ? $gfxData->data->id : null,
            isset($this->style->legend->textTemplate) ? $this->style->legend->textTemplate : null
        );

        return $text->create();
    }

    /**
     * Create the legend
     *
     * @return string
     */
    public function create()
    {
        $sliceList = $this->gfxData->sliceList;
        $html      = $this->createAllLabels($sliceList);

        if (isset($this->style->donutMainLegend) && $this->mode == PIE::DONUT_MODE) {
            $mainLegend = new Text(
                $this->style->donutMainLegend->label,
                $this->gfxData->center,
                $this->style->donutMainLegend->cssClass
            );
            $html .= $mainLegend->create();
        }

        return $html;
    }
}
