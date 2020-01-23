<?php

namespace Cws\Bundle\SVGChartBundle\SVGChart;

use SVG\SVG;

use Cws\Bundle\SVGChartBundle\SVGChart\Pie\Pie;
use Cws\Bundle\SVGChartBundle\SVGChart\Lines\Lines;
use Cws\Bundle\SVGChartBundle\SVGChart\Bars\Bars;

use Cws\Bundle\SVGChartBundle\SVGChart\Legend\PieLegend;

use Cws\Bundle\SVGChartBundle\SVGChart\Legend\LinesChart;

class SVGChart
{
    /**
     * Create a pie chart
     *
     * @param object $data
     * @param object $style
     *
     * @return string
     */
    public static function createPie($data, $style)
    {
        $output      = array();

        $svgChart    = new SVG($style->width, $style->height);
        $svgDocument = $svgChart->getDocument();

        $pie         = new Pie($data, $style);
        $pie->create($svgDocument);


        $output[] = "<div class=\"$style->cssClass\">";
        $output[] = $svgChart->toXMLString();
        $output[] = $pie->getLegend();
        $output[] = '</div>';

        return implode('', $output);
    }

    /**
     * Create a lines chart
     *
     * @param object $data
     * @param object $style
     *
     * @return string
     */
    public static function createLines($data, $style)
    {
        $output      = array();

        $svgChart    = new SVG($style->width, $style->height);
        $svgDocument = $svgChart->getDocument();

        $lines       = new Lines($data, $style);
        $lines->create($svgDocument);


        $output[] = "<div class=\"$style->cssClass\">";
        $output[] = $svgChart->toXMLString();
        $output[] = $lines->getLegend();
        $output[] = '</div>';

        return implode('', $output);
    }

    /**
     * Create a bars chart
     *
     * @param object $data
     * @param object $style
     *
     * @return string
     */
    public static function createBars($data, $style)
    {
        $output      = array();

        $svgChart    = new SVG($style->width, $style->height);
        $svgDocument = $svgChart->getDocument();

        $bars        = new Bars($data, $style);
        $bars->create($svgDocument);


        $output[] = "<div class=\"$style->cssClass\">";
        $output[] = $svgChart->toXMLString();
        $output[] = $bars->getLegend();
        $output[] = '</div>';

        return implode('', $output);
    }
}
