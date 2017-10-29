<?php

/**
 * Row in table.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 20 Feb.
 */
final class Row extends Component {

    /**
     * Row's id.
     * @var string
     */
    private $id;

    /**
     * Default construct.
     * @param string $id Row's id.
     */
    public function __construct(string $id = '') {
        parent::__construct('tr', '');
        $this->id = $id;
    }

    /**
     * @return string The representation HTML row.
     */
    public function __toString(): string {
        $id = (!empty($this->id)) ? " id=\"" . $this->id . "\"" : '';
        $row = "<tr" . $id . ">";
        foreach ($this->components as $colum) {
            if ($colum instanceof Column) {
                $row .= $colum;
            }
        }

        $row .= "</tr>";
        return $row;
    }

}
