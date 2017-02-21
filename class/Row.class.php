<?php

/**
 * Row in table.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 20 Feb.
 */
final class Row {

    /**
     * Array with columns in this row.
     * @var type Array of Columns
     */
    private $columns;

    /**
     * Default contructor.
     */
    public function __construct() {
        $this->columns = array();
    }

    /**
     * Add a column to this row.
     * @param Column $column Column in this row.
     */
    public function addColumn(Column $column) {
        $index = count($this->columns);
        $this->columns[$index] = $column;
    }

    /**
     * The representation HTML row.
     */
    public function __toString(): string {
        $row = "<tr>";
        foreach ($this->columns as $colum) {
            if ($colum instanceof Column) {
                $row .= $colum;
            }
        }

        $row .= "</tr>";
        return $row;
    }

}
