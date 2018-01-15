<?php
/**
 * Created by PhpStorm.
 * User: joao
 * Date: 14/10/17
 * Time: 16:26
 */

use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase {

    /**
     * Test to correct a past bug.
     *
     * @test
     */
    public function testVlUtilizado() {
        $query = Query::getInstance()->exe("SELECT id FROM itens;");
        $error = false;
        $i = 0;
        if ($query->num_rows > 0) {
            while ($obj = $query->fetch_object()) {
                $item = new Item($obj->id);
                $actual = $item->getVlUtilizado();
                $expected = ($item->getQtUtilizado() * $item->getVlUnitario());

                $epsilon = 0.001;

                if (abs($expected - $actual) >= $epsilon) {
                    $error = true;
                    $i++;
                    echo "Item quebrado: " . $item->getId() . " Expected: " . $expected . " Actual: " . $actual . "\n";
                }
            }
        }
        $this->assertFalse($error, "Itens quebrados: " . $i);
    }

}
