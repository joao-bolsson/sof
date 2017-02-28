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
     * Row's id.
     * @var type String
     */
    private $id;

    /**
     * Default contructor.
     * @param string $id Row's id.
     */
    public function __construct(string $id = '') {
        $this->columns = array();
        $this->id = $id;
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
        $id = (!empty($this->id)) ? " id=\"" . $this->id . "\"" : '';
        $row = "<tr" . $id . ">";
        foreach ($this->columns as $colum) {
            if ($colum instanceof Column) {
                $row .= $colum;
            }
        }

        $row .= "</tr>";
        return $row;
    }

}
