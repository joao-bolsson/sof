<?php

use PHPUnit\Framework\TestCase;

/**
 * Test class Geral and the data in DB.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 18 Mar.
 */
class GeralTest extends TestCase {

    /**
     * Tests the requests total with its sum of items values.
     */
    public function testRequestsValues() {
        $query = Query::getInstance()->exe("SELECT id, valor FROM pedido;");

        while ($obj = $query->fetch_object()) {
            $ped = Query::getInstance()->exe("SELECT sum(valor) AS soma FROM itens_pedido WHERE id_pedido = " . $obj->id);
            $obj_ped = $ped->fetch_object();
            $total = number_format($obj->valor, 3, '.', '');
            $sum = number_format($obj_ped->soma, 3, '.', '');
            $this->assertEquals($total, $sum, "Pedido " . $obj->id . " quebrado");
        }
    }

}
