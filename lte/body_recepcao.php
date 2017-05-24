<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Processos</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                    </button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div class="margin">
                    <button class="btn btn-primary" type="button" onclick="addProcesso(' ', 0)"><i
                                class="fa fa-plus"></i>&nbsp;Adicionar Processo
                    </button>
                </div>
                <table class="table stripe" id="tableRecepcao" style="width: 100%;">
                    <thead>
                    <tr>
                        <th></th>
                        <th>PROCESSO</th>
                        <th>TIPO</th>
                        <th>ESTANTE</th>
                        <th>PRATELEIRA</th>
                        <th>ENTRADA EM</th>
                        <th>SAIDA EM</th>
                        <th>RESPONSÁVEL</th>
                        <th>RETORNO EM</th>
                        <th>OBS</th>
                    </tr>
                    </thead>
                    <tbody id="conteudoRecepcao"></tbody>
                </table>
            </div><!-- ./box-boxy -->
        </div><!-- ./box -->
    </div> <!-- ./col-xs-12 -->
</div><!-- ./row -->
