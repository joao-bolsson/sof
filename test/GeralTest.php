<?php

use PHPUnit\Framework\TestCase;
/**
 * Test class Geral.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 18 Mar.
 */
class GeralTest extends TestCase {

    public function setUp() {
        // empty
    }

    /**
     * Function that tests
     */
    public function testRequestsValues() {
        $this->assertEquals(3, 1 + 2, 'Não somou corretamente');
    }

    public function tearDown() {
        // empty
    }
}
