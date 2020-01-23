<?php

namespace CwsBundle\SVGChart\Tools;

class Text
{
    public $id;
    public $text;
    public $coords;
    public $cssClass;
    public $color;
    public $isRightPositioned;

    /**
     * Text constructor
     *
     * @param string $text
     * @param Point $coords
     * @param string $cssClass
     * @param string $color
     * @param boolean $isRightPositioned
     * @param string $id
     */
    public function __construct($text, Point $coords, $cssClass, $color = '', $isRightPositioned = false, $id = null)
    {
        $this->text = $text;
        $this->coords = $coords;
        $this->cssClass = $cssClass;
        $this->color = $color;
        $this->isRightPositioned = $isRightPositioned;
        $this->id = $id;
    }

    /**
     * Create a HTML div with the text inside
     *
     * @return string
     */
    public function create()
    {
        $result = array();

        $result[] = '<div ';
        if (isset($this->id)) {
            $result[] = "data-id=\"$this->id\" ";
        }
        $result[] = "class=\"$this->cssClass\" ";
        $result[] = 'style="';
        $result[] = 'top:'.$this->coords->y.'px;';

        if ($this->isRightPositioned) {
            $result[] = 'right:'.$this->coords->x.'px;';
        } else {
            $result[] = 'left:'.$this->coords->x.'px;';
        }
        if ($this->color != '') {
            $result[] = 'color:'.$this->color.';';
        }

        $result[] = '"';
        $result[] = '>';
        $result[] = $this->text;
        $result[] = '</div>';

        return implode('', $result);
    }
}
