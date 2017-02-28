<?php

/**
 * Small tag HTML representation.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 21 Feb.
 */
class Small {

    private $class, $text, $attr, $title;

    /**
     * Construct a Small element.
     * 
     * @param string $class Small's class.
     * @param string $text Text that will be show inside <small></small>
     * @param string $attr Other attributes for tag <small>
     * @param string $title Small's title.
     */
    public function __construct(string $class = '', string $text = '', string $attr = '', string $title = '') {
        $this->class = $class;
        $this->text = $text;
        $this->attr = $attr;
        $this->title = $title;
    }

    public function __toString(): string {
        $class = (!empty($this->class)) ? " class=\"" . $this->class . "\"" : '';

        $attr = (!empty($this->attr)) ? " " . $this->attr : '';

        $title = (!empty($this->title)) ? " title=\"" . $this->title . "\"" : '';

        return "<small" . $class . $attr . $title . ">" . $this->text . "</small>";
    }

}
