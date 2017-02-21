<?php

/**
 * Table that will visible to the user in HTML.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 20 Feb.
 */
class Table {

    /**
     * Rows in this table.
     * @var type Array of Rows
     */
    private $rows;

    /**
     * Table's id.
     * @var type String
     */
    private $id;

    /**
     * Classes of this table.
     * @var type String
     */
    private $class;

    /**
     * Strings array with the header's fields.
     * @var type Array of Strings
     */
    private $headers;

    /**
     * Flag that determine if <tbody></tbody> must be build or just its content.
     * @var type Bool
     */
    private $build_body;

    public function __construct(string $id, string $class, array $headers, bool $build_body) {
        $this->rows = array();
        $this->id = $id;
        $this->class = $class;
        $this->headers = $headers;
        $this->build_body = $build_body;
    }

    public function addRow(Row $row) {
        $index = count($this->rows);
        $this->rows[$index] = $row;
    }

    public function __toString(): string {
        $table = "";
        $must_close = false;
        if (!empty($this->id) || !empty($this->class)) {
            $id = $class = '';
            if (!empty($this->id)) {
                $id = "id=\"" . $this->id . "\"";
            }
            if (!empty($this->class)) {
                $class = "class=\"" . $this->class . "\"";
            }

            $table = "<table " . $id . " " . $class . ">";
            $must_close = true;
        }

        $table .= self::buildHeader();
        $table .= self::buildBody();

        if ($must_close) {
            $table .= "</table>";
        }

        return $table;
    }

    /**
     * Build the header of this table.
     * @return string Table's header.
     */
    private function buildHeader(): string {
        if (empty($this->headers)) {
            return '';
        }
        $header = "<thead><tr>";

        foreach ($this->headers as $value) {
            $header .= "<th>" . $value . "</th>";
        }

        $header .= "</tr></thead>";
        return $header;
    }

    /**
     * Build the body of this table.
     * @return string Table's body.
     */
    private function buildBody(): string {
        $body = '';
        if ($this->build_body) {
            $body = "<tbody>";
        }

        foreach ($this->rows as $row) {
            if ($row instanceof Row) {
                $body .= $row;
            }
        }

        if ($this->build_body) {
            $body .= "</tbody>";
        }
        return $body;
    }

}
