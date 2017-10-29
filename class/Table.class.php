<?php

/**
 * Table that will visible to the user in HTML.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 20 Feb.
 */
final class Table extends Component {

    /**
     * Table's id.
     * @var string
     */
    private $id;

    /**
     * Strings array with the header's fields.
     * @var array of Strings
     */
    private $headers;

    /**
     * Flag that determine if <tbody></tbody> must be build or just its content.
     * @var bool
     */
    private $build_body;

    /**
     * Default construct.
     *
     * @param string $id <table> id.
     * @param string $class <table> class.
     * @param array $headers Fields in <thead>, if empty: <thead> thead tag will is not build.
     * @param bool $build_body if true: the table must have the tag <tbody>, else - just its rows.
     */
    public function __construct(string $id = '', string $class = '', array $headers = [], bool $build_body = false) {
        parent::__construct('table', $class);
        $this->id = $id;
        $this->headers = $headers;
        $this->build_body = $build_body;
    }

    /**
     * @return string String representation of this table.
     */
    public function __toString(): string {
        $table = "";
        $must_close = false;
        if (!empty($this->id) || !empty($this->class) || $this->build_body) {
            $id = (!empty($this->id)) ? " id=\"" . $this->id . "\"" : '';
            $class = (!empty($this->class)) ? " class=\"" . $this->class . "\"" : '';

            $table = "<table" . $id . $class . ">";
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

        if (!$this->isEmpty()) {
            foreach ($this->components as $row) {
                if ($row instanceof Row) {
                    $body .= $row;
                }
            }
        } else {
            $count_h = count($this->headers);
            $body .= "<tr><td colspan='" . $count_h . "'>Nenhum registro na tabela</td></tr>";
        }

        if ($this->build_body) {
            $body .= "</tbody>";
        }
        return $body;
    }

}
