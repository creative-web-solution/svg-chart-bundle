<?php

namespace Cws\Bundle\SVGChartBundle\SVGChart\Tools;

class Text
{
    public $id;
    public $text;
    public $coords;
    public $cssClass;
    public $color;
    public $isRightPositioned;
    public $template;

    /**
     * Text constructor
     *
     * @param string $text
     * @param Point $coords
     * @param string $cssClass
     * @param string $color
     * @param boolean $isRightPositioned
     * @param string $id
     * @param string $template
     */
    public function __construct(
        $text,
        Point $coords,
        $cssClass,
        $color = '',
        $isRightPositioned = false,
        $id = null,
        $template = '<div {{ATTR}}>{{TEXT}}</div>'
    )
    {
        $this->text = $text;
        $this->coords = $coords;
        $this->cssClass = $cssClass;
        $this->color = $color;
        $this->isRightPositioned = $isRightPositioned;
        $this->id = $id;
        $this->template = $template;
    }

    /**
     * Create a HTML div with the text inside
     *
     * @return string
     */
    public function create()
    {
        $attr = array();

        if (isset($this->id)) {
            $attr[] = "data-id=\"$this->id\"";
        }

        $attr[] = "class=\"$this->cssClass\"";

        $style = 'style="';
        $style .= 'top:'.$this->coords->y.'px;';
        if ($this->isRightPositioned) {
            $style .= 'right:'.$this->coords->x.'px;';
        } else {
            $style .= 'left:'.$this->coords->x.'px;';
        }
        if ($this->color != '') {
            $style .= 'color:'.$this->color.';';
        }
        $style .= '"';

        $attr[] = $style;


        return str_replace(
            array('{{ATTR}}', '{{TEXT}}'),
            array(implode(' ', $attr), $this->text),
            $this->template
        );
    }
}
