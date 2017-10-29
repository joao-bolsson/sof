<?php

/**
 * Small tag HTML representation.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 21 Feb.
 */
final class Small extends Component {

    private $attr, $title;

    /**
     * Construct a Small element.
     *
     * @param string $class Small's class.
     * @param string $text Text that will be show inside <small></small>
     * @param string $attr Other attributes for tag <small>
     * @param string $title Small's title.
     */
    public function __construct(string $class = '', string $text = '', string $attr = '', string $title = '') {
        parent::__construct('small', $class, $text);
        $this->attr = $attr;
        $this->title = $title;
    }

    /**
     * @param Component $component Component to add.
     */
    public function addComponent(Component $component) {
        // empty
    }

    /**
     * @return string String representation of this small element.
     */
    public function __toString(): string {
        $class = (!empty($this->class)) ? " class=\"" . $this->class . "\"" : '';

        $attr = (!empty($this->attr)) ? " " . $this->attr : '';

        $title = (!empty($this->title)) ? " title=\"" . $this->title . "\"" : '';

        return "<small" . $class . $attr . $title . ">" . $this->text . "</small>";
    }

}
