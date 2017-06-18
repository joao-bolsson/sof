<?php
/**
 * File with form to edit an item.
 *
 * @author João Bolsson (joaovictorbolsson@gmail.com)
 * @since 2017, 05 Jun.
 */
?>
<form id="formEditRegItem">
    <input id="id" type="hidden" name="id" value="0"/>
    <input type="hidden" name="form" value="formEditRegItem"/>
    <input type="hidden" name="admin" value="1"/>
    <div class="modal-body">
        <?php include_once __DIR__ .'/formItem.html' ?>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" type="submit" style="width: 100%;"><i class="fa fa-refresh"></i>&nbsp;Atualizar / Cadastraaaaaar
        </button>
    </div>
</form>
