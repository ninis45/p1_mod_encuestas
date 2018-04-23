<ul class="modal_select">
    <?php foreach($cuestionarios as $cuestionario):?>
        <li><a confirm-action href="<?=base_url('admin/encuestas/asignacion/create/'.$cuestionario->id)?>"> <?=$cuestionario->titulo?></a></li>
    <?php endforeach;?>
</ul>