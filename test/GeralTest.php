<?php

use PHPUnit\Framework\TestCase;

/**
 * Test class Geral and the data in DB.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 18 Mar.
 */
class GeralTest extends TestCase {

    public function setUp() {
        // empty
    }

    /**
     * Tests the actual sectors balance with all budget releases.
     */
    public function testBalances() {
        $query = Query::getInstance()->exe("SELECT id_setor, saldo FROM saldo_setor");
        while ($setor = $query->fetch_object()) {
            $balance = 0;
            // categoria: adiantamento
            $obj_adi = Query::getInstance()->exe("SELECT sum(valor_adiantado) AS adiantamentos FROM saldos_adiantados WHERE id_setor = " . $setor->id_setor . " AND status = 1")->fetch_object();
            $balance += ($obj_adi->adiantamentos != NULL) ? $obj_adi->adiantamentos : 0;

            // categoria: normal
            $obj_normal = Query::getInstance()->exe("SELECT sum(valor) AS normal FROM saldos_lancamentos WHERE categoria = 1 AND id_setor = " . $setor->id_setor)->fetch_object();
            $balance += ($obj_normal->normal != NULL) ? $obj_normal->normal : 0;


            // categoria: transferencia
            $obj_transf = Query::getInstance()->exe("SELECT sum(valor) AS transf FROM saldos_lancamentos WHERE categoria = 3 AND id_setor = " . $setor->id_setor)->fetch_object();
            $balance += ($obj_transf->transf != NULL) ? $obj_transf->transf : 0;

            // categoria: antecipação
            $obj_ant = Query::getInstance()->exe("SELECT sum(valor) AS antecip FROM saldos_lancamentos WHERE categoria = 4 AND id_setor = " . $setor->id_setor)->fetch_object();

            $balance += ($obj_ant->antecip != NULL) ? $obj_ant->antecip : 0;

            // pedidos em análise // podem voltar para o saldo do setor
            $obj_an = Query::getInstance()->exe('SELECT sum(valor) AS soma FROM pedido WHERE id_setor = ' . $setor->id_setor . ' AND status = 2')->fetch_object();
            $balance += ($obj_an->soma != NULL) ? $obj_an->soma : 0;

            $balance = number_format($balance, 3, '.', '');
            $setor->saldo = number_format($setor->saldo, 3, '.', '');

            echo "Setor " . $setor->id_setor . ": " . $balance . " -> " . $setor->saldo . "\n";
            $this->assertEquals($balance, $setor->saldo, "Saldo quebrado");

        }
    }

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

    public function tearDown() {
        // empty
    }
}
