<section>
    <div class="lead text-success"><?=lang('encuesta:head_cuestionarios')?></div>
    <hr />
    <div class="row">
        <?php foreach($cuestionarios as $cuestionario){?>
            <div class="col-lg-4">
                <div class="card bg-primary">
                    <div class="card-content">
                                    <span class="card-title" title="<?=$cuestionario->titulo?>"><?=resume($cuestionario->titulo,40)?></span>
                                    <p></p>
                                    </div>
                                    <div class="card-action">
                                        <a href="<?=base_url('admin/encuestas/cuestionario/'.$cuestionario->id)?>" class="btn btn-default color-primary"><span>Administrar</span></a>
                                        
                                    </div>
                </div> 
            </div>
    
    
        <?php }?>
    </div>
    <div class="lead text-success"><?=lang('encuesta:head_asignaciones')?></div>
    <hr />
    
    <div class="row">
        <?php foreach($asignaciones as $cuestionario){?>
            <div class="col-lg-4">
                <div class="card bg-primary">
                    <div class="card-content">
                                    <span class="card-title" title="<?=$cuestionario->titulo?>"><?=resume($cuestionario->titulo,30)?></span>
                                    <p></p>
                                    </div>
                                    <div class="card-action">
                                        <a href="<?=base_url('admin/encuestas/load/'.$cuestionario->id_cuestionario.'/'.$cuestionario->id)?>" class="btn btn-default color-primary"><span>Administrar</span></a>
                                        
                                    </div>
                </div> 
            </div>
    
    
        <?php }?>
    </div>
</section>