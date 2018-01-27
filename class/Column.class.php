<?php

defined('DEFAULT_FONT_SIZE') or define('DEFAULT_FONT_SIZE', 0);

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
     * @var int Font size.
     */
    private $fontSize;

    /**
     * Default construct.
     * @param string $content Column's content.
     * @param string $display Column's dislay.
     */
    public function __construct(string $content, string $display = '') {
        parent::__construct('td', '', $content);
        $this->display = $display;
        $this->fontSize = DEFAULT_FONT_SIZE;
    }

    /**
     * @param int $fontSize
     */
    public function setFontSize(int $fontSize) {
        $this->fontSize = $fontSize;
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
        $style = " style=\"";

        if (!empty($this->display)) {
            $style .= "display: " . $this->display . ";";
        }

        if ($this->fontSize != DEFAULT_FONT_SIZE) {
            $style .= "font-size: " . $this->fontSize . ";";
        }

        $style .= "\"";
        return "<td" . $style . ">" . $this->text . "</td>";
    }

}
