<?php

use PHPUnit\Framework\TestCase;

require __DIR__ . "/../class/Query.class.php";

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
        echo "[GeralTest:testRequestsValues]\n";
        $query = Query::getInstance()->exe("SELECT id, round(valor, 3) AS valor FROM pedido;");

        $error = false;
        while ($obj = $query->fetch_object()) {
            $ped = Query::getInstance()->exe("SELECT round(sum(valor), 3) AS soma FROM itens_pedido WHERE id_pedido = " . $obj->id);
            $obj_ped = $ped->fetch_object();
            if ($obj->valor != $obj_ped->soma) {
                echo "Pedido " . $obj->id . " quebrado: Valor: " . $obj->valor . " | Soma dos itens: " . $obj_ped->soma . "\n";
                $error = true;
            }
        }
        $this->assertEquals($error, false, "Pedidos quebrados");
    }

    // NOTE: never test the request values and its items values (vl_unitario can change - only changes requests values if its is before analysis.

}
