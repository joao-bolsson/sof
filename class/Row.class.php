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
        $id = '';
        if (!empty($this->id)) {
            $id = " id=\"" . $this->id . "\"";
        }
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
