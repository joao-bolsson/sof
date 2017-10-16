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
     * Test if some methods works with null parameters.
     * @test
     */
    public function testNull() {
        $item = new Item(1);
        $this->assertNotNull($item->getChave(), "Chave é null");

        $item->setChave(null); // this cann't update
        $this->assertNotNull($item->getChave(), "Chave é null");
    }

}
