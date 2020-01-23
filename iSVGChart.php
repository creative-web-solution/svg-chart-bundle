<?php

namespace CwsBundle\SVGChart;

use SVG\Nodes\Structures\SVGDocumentFragment;

interface ISVGChart
{
    public function create(SVGDocumentFragment $svgDocument);
}
