<?php
/**
 * Created by PhpStorm.
 * User: joao
 * Date: 28/08/17
 * Time: 23:14
 */

use PHPUnit\Framework\TestCase;

class SectorTest extends TestCase {

    /**
     * @test
     */
    public function testMoney() {
        echo "[SectorTest:testMoney]\n";
        $query = Query::getInstance()->exe("SELECT id FROM setores WHERE id > 1");
        if ($query->num_rows > 0) {
            while ($obj = $query->fetch_object()) {
                $sector = new Sector($obj->id);

                $moneySources = $sector->getMoneySources();
                if (!empty($moneySources)) {
                    $moneyInSources = 0.0;
                    foreach ($moneySources as $source) {
                        if ($source instanceof MoneySource) {
                            $moneyInSources += $source->getValue();
                        }
                    }
                    $this->assertEquals($sector->getMoney(), $moneyInSources, "Setor com saldo quebrado: " . $sector->getId(), 3);
                }

            }
        }
    }
}
