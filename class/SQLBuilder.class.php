<?php

/**
 * Class SQLBuilder.class.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 26 May.
 */
class SQLBuilder {

    /**
     * @var int Predefined SQL types.
     */
    static $INSERT = 0, $UPDATE = 1, $SELECT = 2;

    /**
     * @var array The SQL prefix by according with predefined SQL types.
     */
    private static $SQL_PREFIX = ["INSERT INTO", "UPDATE", "SELECT"];

    private $type, $tables, $columns, $values, $where;

    /**
     * SQLBuilder.class constructor.
     *
     * Note: this class supports only SQLs with te following formats:
     *
     * SELECT columns FROM tables WHERE where.
     *
     * @param int $type Must be a predefined type in SQLBuilder.class class.
     */
    function __construct(int $type) {
        $this->type = $type;
        $this->columns = [];
        $this->tables = [];
        $this->values = [];
        $this->where = "";
    }

    /**
     * Optional method.
     *
     * Don't put WHERE keyword.
     *
     * Accept format: "(where condition) ORDER BY (statement) LIMIT (number)"
     *
     * @param string $where Where and Order by.
     */
    function setWhere(string $where) {
        $this->where = " WHERE " . $where;
    }

    /**
     * @param array $tables Tables to execute the final SQL.
     */
    function setTables(array $tables) {
        $this->tables = $tables;
    }

    /**
     * If the final SQL must use more than 1 one, specify the columns with format "table.column".
     *
     * @param array $columns Columns to execute in final SQL.
     */
    function setColumns(array $columns) {
        $this->columns = $columns;
    }

    /**
     * @param array $values The values to insert or update.
     */
    function setValues(array $values) {
        $this->values = $values;
    }

    private function buildColumns(): string {
        if ($this->type == self::$UPDATE) {
            return " SET ";
        }
        $columns = "";
        for ($i = 0; $i < count($this->columns); $i++) {
            $columns .= $this->columns[$i];
            if ($i != count($this->columns) - 1) {
                $columns .= ", ";
            }
        }
        if ($this->type == self::$INSERT) {
            return "(" . $columns . ")";
        } else if ($this->type == self::$SELECT) {
            $columns .= " FROM ";
        }
        return $columns;
    }

    private function buildTables(): string {
        $tables = "";
        for ($i = 0; $i < count($this->tables); $i++) {
            $tables .= $this->tables[$i];
            if ($i != count($this->tables) - 1) {
                $tables .= ", ";
            }
        }
        return $tables;
    }

    private function buildValues(): string {
        $values = "";
        for ($i = 0; $i < count($this->values); $i++) {
            $value = $this->values[$i];
            if (is_string($value)) {
                $value = "\"" . $value . "\"";
            } else if (is_null($value)) {
                $value = "NULL";
            } else if (is_bool($value)) {
                $value = intval($value);
            }

            if ($this->type == self::$UPDATE) {
                $column = $this->columns[$i];
                $values .= $column . "=" . $value;
            } else {
                $values .= $value;
            }

            if ($i != count($this->values) - 1) {
                $values .= ", ";
            }
        }
        if ($this->type == self::$INSERT) {
            return " VALUES (" . $values . ")";
        }
        return $values;
    }

    /**
     * @return string The final SQL.
     */
    function __toString(): string {
        $prefix = self::$SQL_PREFIX[$this->type] . " ";

        $tables = $this->buildTables();

        $columns = $this->buildColumns();

        $values = $this->buildValues();

        $sql = $prefix . $tables . $columns . $values . $this->where;

        if ($this->type == self::$SELECT) {
            $sql = $prefix . $columns . $tables . $this->where;
        }

        return $sql;
    }

}