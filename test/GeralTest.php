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
        $query = Query::getInstance()->exe("SELECT id, round(valor, 3) AS valor FROM pedido;");

        $error = false;
        while ($obj = $query->fetch_object()) {
            $ped = Query::getInstance()->exe("SELECT round(sum(valor), 3) AS soma FROM itens_pedido WHERE id_pedido = " . $obj->id);
            $obj_ped = $ped->fetch_object();
            if ($obj->valor != $obj_ped->soma) {
                echo "Pedido " . $obj->id . " quebrado\n";
                $error = true;
            }
        }
        $this->assertEquals($error, false, "Pedidos quebrados");
    }

}
