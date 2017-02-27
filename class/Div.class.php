<?php

/**
 * Component that represents a HTML div.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 27 Feb.
 */
class Div implements Component {

    /**
     * Div's class.
     * @var type string
     */
    private $class;

    /**
     * Array with components inside <div></div>
     * @var type array
     */
    private $components;

    /**
     * Default construct.
     * @param string $class Classes of this div.
     */
    public function __construct(string $class = '') {
        $this->class = $class;
        $this->components = [];
    }

    public function addComponent(Component $component) {
        $i = count($this->components);
        $this->components[$i] = $component;
    }

    public function __toString() {
        $class = (!empty($this->class)) ? " class=\"" . $this->class . "\"" : '';
        $div = "<div" . $class . ">";

        $len = count($this->components);
        for ($i = 0; $i < $len; $i++) {
            $div .= $this->components[$i];
        }

        $div .= '</div>';
        return $div;
    }

}
