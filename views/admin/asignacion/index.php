<section>
    <div class="lead text-success"><?=lang('asignacion:title')?></div>
    <?php echo form_open($this->uri->uri_string() ,'class="form-inline" method="get" ') ?>
    
    <table class="table">
        <thead>
            <tr>
                <th>Titulo</th>
                <th>Vinculado</th>
                <th width="16%"></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($asignaciones as $asignacion):?>
            <tr>
                <td><?=$asignacion->titulo?></td>
                <td><?=$asignacion->table?></td>
                <td>
                    <?php echo anchor('admin/encuestas/delete/'.$asignacion->id, lang('buttons:delete'), 'class="button" confirm-action') ?> |
                    
                    <?php echo anchor('admin/encuestas/asignacion/truncate/'.$asignacion->id, lang('asignacion:truncate'), 'class="button" confirm-action') ?> |
                    <?php echo anchor('admin/encuestas/asignacion/edit/'.$asignacion->id, lang('buttons:edit'), 'class="button edit"') ?>
                    
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
    <?php echo form_close();?>
</section>