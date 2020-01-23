<?php

namespace Cws\Bundle\SVGChartBundle\SVGChart;

use SVG\Nodes\Structures\SVGDocumentFragment;

interface ISVGChart
{
    public function create(SVGDocumentFragment $svgDocument);
}
