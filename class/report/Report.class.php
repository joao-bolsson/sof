<?php
/**
 * Interface to implements any report.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2017, 29 Oct.
 */

interface Report {

    /**
     * @return string The report header.
     */
    function buildHeader(): string;

    /**
     * @return string The report body.
     */
    function buildBody(): string;

    /**
     * @return string The report footer.
     */
    function buildFooter(): string;

    /**
     * @return string The string representation of the report.
     */
    function __toString(): string;

}