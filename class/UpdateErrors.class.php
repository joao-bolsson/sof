<?php
/**
 * @author João Bolsson (joaovictorbolsson@gmail.com).
 * @since 2018, 15 Jan.
 */

spl_autoload_register(function (string $class_name) {
    include_once $class_name . '.class.php';
});

class UpdateErrors {

    /**
     * Temporary code.
     */
    public static function checkItems() {
        $query = Query::getInstance()->exe("SELECT id FROM itens;");
        if ($query->num_rows > 0) {
            while ($obj = $query->fetch_object()) {
                $item = new Item($obj->id);
                $actual = $item->getVlUtilizado();
                $expected = ($item->getQtUtilizado() * $item->getVlUnitario());

                $epsilon = 0.001;

                if (abs($expected - $actual) >= $epsilon) {
                    Query::getInstance()->exe("UPDATE itens SET vl_utilizado = " . $expected . " WHERE id = " . $item->getId());
                }
            }
        }
    }

    public static function scanDataBase() {
        $query = Query::getInstance()->exe("SELECT id, round(valor, 3) AS valor FROM pedido;");

        while ($obj = $query->fetch_object()) {
            $ped = Query::getInstance()->exe("SELECT round(sum(valor), 3) AS soma FROM itens_pedido WHERE id_pedido = " . $obj->id);
            $obj_ped = $ped->fetch_object();
            $total = $obj->valor;
            $sum = $obj_ped->soma;
            if ($total != $sum) {
                echo "Corrige pedido: " . $obj->id . "\n";
                Query::getInstance()->exe("UPDATE pedido SET valor = '" . $sum . "' WHERE id = " . $obj->id);
            }
        }
    }

    public static function verifySectors() {
        $query = Query::getInstance()->exe("SELECT id FROM setores WHERE id > 1;");
        while ($obj = $query->fetch_object()) {
            $sector = new Sector($obj->id);
            $sector->updateMoney();
        }
    }

    public static function checkSIAFI() {
        $query = Query::getInstance()->exe("SELECT pedido_contrato.id_pedido, pedido_empenho.empenho FROM pedido_contrato, pedido_empenho WHERE pedido_contrato.id_pedido = pedido_empenho.id_pedido AND pedido_contrato.id_tipo = 1;");
        if ($query->num_rows > 0) {
            while ($obj = $query->fetch_object()) {
                if (!empty($obj->empenho)) {
                    Query::getInstance()->exe("UPDATE pedido_contrato SET siafi = '" . $obj->empenho . "' WHERE id_pedido = " . $obj->id_pedido);
                }
            }
        }
    }
}