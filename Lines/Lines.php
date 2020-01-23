<?php

namespace CwsBundle\SVGChart\Lines;

use CwsBundle\SVGChart\Tools\Point;
use CwsBundle\SVGChart\Tools\Line;
use CwsBundle\SVGChart\ISVGChart;
use SVG\Nodes\Structures\SVGDocumentFragment;
use SVG\Nodes\Structures\SVGGroup;
use SVG\Nodes\Shapes\SVGPath;
use SVG\Nodes\Shapes\SVGCircle;

class Lines implements ISVGChart
{
    private $gfxData;
    private $globalGfxData;
    private $style;
    private $linesList;

    /**
     * Lines constructor.
     *
     * @param object $data
     * @param object $style
     */
    public function __construct($data, $style)
    {
        $this->style         = $style;
        $this->gfxData       = $this->computeGfxData($data, $style);
        $this->globalGfxData = $this->computeGlobalGfxData($this->gfxData);
    }

    /**
     * Compute points list, min and max value of ONE line
     *
     * @param object $lineData
     * @param object $style
     * @param int $horizontalStep
     *
     * @return object
     */
    private function computePointsList($lineData, $style, $horizontalStep)
    {
        $pointsList = array();
        $ordMinMax  = array();
        $absSteps   = array();
        $min        = $style->axes->ord->min;
        $max        = $style->axes->ord->max;
        $delta      = $max - $min;

        foreach ($lineData->values as $key => $value) {
            $pointsList[] = new Point(
                $style->canvas->left + $style->canvas->marginX + $horizontalStep * $key,
                $style->canvas->top + $style->canvas->height - round(
                    ($value->value - $min) * $style->canvas->height / $delta,
                    3
                )
            );

            if (isset($ordMinMax[$key])) {
                // /!\ WARNING here: The higher the value is, the smaller its top coordinate is.
                // So min and max are inverted
                $ordMinMax[$key]->min = max($ordMinMax[$key]->min, $pointsList[$key]->y);
                $ordMinMax[$key]->max = min($ordMinMax[$key]->max, $pointsList[$key]->y);
            } else {
                $ordMinMax[] = (object) array(
                    'min' => $pointsList[$key]->y,
                    'max' => $pointsList[$key]->y
                );
            }

            $absSteps[] = $pointsList[$key]->x;
        }

        return (object) array(
            'pointsList' => $pointsList,
            'ordMinMax'  => $ordMinMax,
            'absSteps'   => $absSteps
        );
    }

    /**
     * Compute the min, max coordinate over ALL lines
     *
     * @param array $gfxData
     * @return object
     */
    private function computeGlobalGfxData($gfxData)
    {
        $globalOrdMinMax = array();
        $globalAbsSteps  = array();

        foreach ($gfxData as $currentLineData) {
            foreach ($currentLineData->ordMinMax as $key => $ordMinMax) {
                if (isset($globalOrdMinMax[$key])) {
                    // /!\ WARNING here: The higher the value is, the smaller its top coordinate is.
                    // So min and max are inverted
                    $globalOrdMinMax[$key]->min = max($globalOrdMinMax[$key]->min, $ordMinMax->min);
                    $globalOrdMinMax[$key]->max = min($globalOrdMinMax[$key]->max, $ordMinMax->max);
                } else {
                    $globalOrdMinMax[] = (object) array(
                        'min' => $ordMinMax->min,
                        'max' => $ordMinMax->max
                    );
                }
            }

            foreach ($currentLineData->absSteps as $key => $absSteps) {
                if (isset($globalAbsSteps[$key])) {
                    $globalAbsSteps[$key] = $absSteps;
                } else {
                    $globalAbsSteps[] = $absSteps;
                }
            }
        }

        return (object) array(
            'ordMinMax' => $globalOrdMinMax,
            'absSteps'  => $globalAbsSteps,
            'top'       => $this->style->canvas->top,
            'bottom'    => $this->style->canvas->top + $this->style->canvas->height,
            'left'      => $this->style->canvas->left,
            'right'     => $this->style->canvas->left + $this->style->canvas->width,
            'width'     => $this->style->canvas->width,
            'height'    => $this->style->canvas->height
        );
    }

    /**
     * Compute all the data required to draw the chart
     *
     * @param object $data
     * @param object $style
     *
     * @return array
     */
    private function computeGfxData($data, $style)
    {
        $gfxData = array();

        $horizontalStep = round(
            ($style->canvas->width - $style->canvas->marginX * 2) / count($style->axes->abs->labels),
            3
        );

        foreach ($data as $currentLineData) {
            $pointsList = $this->computePointsList($currentLineData, $style, $horizontalStep);
            $lineData = (object) array(
                'horizontalStep' => $horizontalStep,
                'data'           => $currentLineData,
                'pointsList'     => $pointsList->pointsList,
                'ordMinMax'      => $pointsList->ordMinMax,
                'absSteps'       => $pointsList->absSteps
            );

            $gfxData[] = $lineData;
        }

        return $gfxData;
    }

    /**
     * Create one line and all its bullets
     *
     * @param object $lineGfxData
     * @param object $style
     *
     * @return SVGPath
     */
    private function createLineAndBullets($lineGfxData, $style)
    {
        $svgLine = array();
        $bullets = null;

        if ($lineGfxData->data->displayBullets) {
            $bullets = new SVGGroup();

            if (isset($lineGfxData->data->id)) {
                $bullets->setAttribute('data-id', $lineGfxData->data->id);
            }

            if (isset($lineGfxData->data->cssBulletListClass)) {
                $bullets->setAttribute('class', $lineGfxData->data->cssBulletListClass);
            }
        }

        foreach ($lineGfxData->pointsList as $index => $point) {
            $svgLine[] = $index == 0 ? Line::lineFrom($point) : Line::lineTo($point);

            if ($lineGfxData->data->displayBullets) {
                $bullet = new SVGCircle($point->x, $point->y, $lineGfxData->data->bulletRadius);
                $bullet->setAttribute(
                    'stroke',
                    $lineGfxData->data->bulletStroke ? $lineGfxData->data->bulletStroke : 'none'
                );
                $bullet->setAttribute(
                    'stroke-width',
                    $lineGfxData->data->bulletStrokeWidth ? $lineGfxData->data->bulletStrokeWidth : 1
                );
                $bullet->setAttribute(
                    'fill',
                    $lineGfxData->data->bulletColor ? $lineGfxData->data->bulletColor : 'none'
                );
                if (isset($lineGfxData->data->baseBulletId)) {
                    $bullet->setAttribute(
                        'data-id',
                        $lineGfxData->data->baseBulletId.'-'.$index
                    );
                }

                if (isset($lineGfxData->data->cssBulletClass)) {
                    $bullet->setAttribute('class', $lineGfxData->data->cssBulletClass);
                }

                $bullets->addChild($bullet);
            }
        }

        $svgPath = implode(' ', $svgLine);

        $line = new SVGPath($svgPath);

        $line->setAttribute('stroke', $lineGfxData->data->color);
        $line->setAttribute('stroke-width', $lineGfxData->data->thickness);
        $line->setAttribute('stroke-linecap', $style->linecap);
        $line->setAttribute('stroke-linejoin', $style->linejoin);
        $line->setAttribute('fill', 'none');

        if (isset($lineGfxData->data->id)) {
            $line->setAttribute('data-id', $lineGfxData->data->id);
        }

        if (isset($lineGfxData->data->cssClass)) {
            $line->setAttribute('class', $lineGfxData->data->cssClass);
        }

        return (object)array(
            'line'    => $line,
            'bullets' => $bullets
        );
    }

    /**
     * Create the lines and bullets list
     *
     * @param array $gfxData
     * @param object $style
     */
    private function createAllLinesAndBullets($gfxData, $style)
    {
        $this->linesList = array();

        foreach ($gfxData as $currentLineData) {
            $this->linesList[] = $this->createLineAndBullets($currentLineData, $style);
        }
    }

    /**
     * Create the chart and its axes and append them to the SVG document
     *
     * @param SVGDocumentFragment $svgDocument
     *
     * @return string
     */
    public function create(SVGDocumentFragment $svgDocument)
    {
        $axes = new Axes($this, $this->style);
        $axes->create($svgDocument);

        $this->createAllLinesAndBullets($this->gfxData, $this->style);

        foreach ($this->linesList as $line) {
            $svgDocument->addChild($line->line);

            if (isset($line->bullets)) {
                $svgDocument->addChild($line->bullets);
            }
        }
    }

    /**
     * Return the HTML of the legend
     *
     * @return string
     */
    public function getLegend()
    {
        $legend = new Legend($this, $this->style);

        return $legend->create();
    }

    /**
     * Return the computed graphics data used to create the drawing
     *
     * @return object
     */
    public function getGfxData()
    {
        return $this->gfxData;
    }

    /**
     * Return the global computed graphics data used to create legend and axes
     * @return object
     */
    public function getGlobalGfxData()
    {
        return $this->globalGfxData;
    }
}
