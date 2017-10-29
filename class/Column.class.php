<?php

/**
 * Column in row.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 20 Feb.
 */
final class Column extends Component {

    /**
     * Column's display.
     * @var string
     */
    private $display;

    /**
     * Default construct.
     * @param string $content Column's content.
     * @param string $display Column's dislay.
     */
    public function __construct(string $content, string $display = '') {
        parent::__construct('td', '', $content);
        $this->display = $display;
    }

    /**
     * @param Component $component Component to add
     */
    public function addComponent(Component $component) {
        // empty for while
    }

    /**
     * @return string String representation of this column.
     */
    public function __toString() {
        $display = (!empty($this->display)) ? " style=\"display: " . $this->display . ";\"" : '';
        return "<td" . $display . ">" . $this->text . "</td>";
    }

}
