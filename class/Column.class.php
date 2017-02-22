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
     * Default construct.
     * @param string $content Column's content.
     */
    public function __construct(string $content) {
        $this->content = $content;
    }

    public function __toString() {
        return "<td>" . $this->content . "</td>";
    }

}
