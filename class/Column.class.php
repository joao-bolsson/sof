<?php

/**
 * Column in row.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 20 Feb.
 */
final class Column {

    /**
     * Column's content.
     * @var type String
     */
    private $content;

    /**
     * Column's display.
     * @var type String
     */
    private $display;

    /**
     * Default construct.
     * @param string $content Column's content.
     * @param string $display Column's dislay.
     */
    public function __construct(string $content, string $display = '') {
        $this->content = $content;
        $this->display = $display;
    }

    public function __toString() {
        $display = (!empty($this->display)) ? " style=\"display: " . $this->display . ";\"" : '';
        return "<td" . $display . ">" . $this->content . "</td>";
    }

}
