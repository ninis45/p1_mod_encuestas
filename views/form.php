<style type="text/css">
    .form-encuesta{
        padding:0px 20px;
    }
</style>
<div class="container">
    <section>
        <div class="row">
           
            <div class="col-md-10">
                <header><h2>Encuesta "<?=$asignacion->titulo?>"</h2></header>
                 {{ theme:partial name="notices" }}
                 <?php echo form_open($this->uri->uri_string().'?'.http_build_query($_GET),($encuesta)?'method="post"':'method="get"');?>
                 <?php if($asignacion->comodin){ ?>
                    <input type="hidden" name="comodin" value="<?=$asignacion->comodin_valor?>" />
                 <?php }?>
                 
                 <?php if($auth){ ?>
                    <?php if ($asignacion->instrucciones):?>
                        <div class="alert alert-info">
                            <?=$asignacion->instrucciones?>
                        </div>
                    <?php endif;?>
                    <?php if(validation_errors()):?>
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                            <?php echo validation_errors(); ?>
                        </div>
                    <?php endif;?>
                    <ul class="nav nav-tabs" id="tabs">
                        <li class="active"><a href="#tab-general" data-toggle="tab">Datos generales</a></li>
                        <li><a href="#tab-form" data-toggle="tab">Encuesta</a></li>
                       
                    </ul><!-- /#my-profile-tabs -->
                    <div class="tab-content my-account-tab-content">
                        <div class="tab-pane active form-encuesta" id="tab-general">
                            <?php $campos = array('edad'=>'Edad','sexo'=>'Sexo','email'=>'Correo electrónico','telefono'=>'Teléfono','social_facebook'=>'Facebook','social_twitter'=>'Twitter','social_instagram'=>'Instagram','social_whatsapp'=>'Whatsapp','social_otro'=>'Otra red social');?>
                            <?php foreach($campos as $field=>$label):?>
                                <?php if(in_array($field,$campos_encuesta)==false) continue;?>
                                <div class="form-group">
                                    <label><?=$label?></label>
                                    <?php switch($field){
                                        case 'sexo':
                                        ?>
                                            <?php echo form_dropdown($field,array(''=>'Elegir','1'=>'Hombre','2'=>'Mujer'),$encuesta->{$field},'class="selectize"') ?>
                                        <?php break;
                                        case 'social_facebook':?>
                                        <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon3"> https://facebook.com/</span>
                                            <?=form_input('social_facebook',$encuesta->{$field},'class="form-control" placeholder="usuario"')?>
                                            <span class="input-group-addon">
                                                <i class="fa fa-facebook"></i>
                                            </span>
                                        </div>
                                        <?php break;
                                        case 'social_instagram':?>
                                        <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon3"> https://instagram.com/</span>
                                            <?=form_input('social_instagram',$encuesta->{$field},'class="form-control" placeholder="usuario"')?>
                                            <span class="input-group-addon">
                                                <i class="fa fa-instagram"></i>
                                            </span>
                                        </div>
                                        <?php break;
                                        case 'social_whatsapp':?>
                                        <div class="input-group">
                                            
                                            <?=form_input('social_whatsapp',$encuesta->{$field},'class="form-control" placeholder="Número  de telefono"')?>
                                            <span class="input-group-addon">
                                                <i class="fa fa-whatsapp"></i>
                                            </span>
                                        </div>
                                        
                                        <?php break;
                                        case 'social_twitter': ?>
                                        <div class="input-group">
                                            <span class="input-group-addon" > https://twitter.com/</span>
                                            <?=form_input('social_twitter',$encuesta->{$field},'class="form-control" placeholder="@usuario"')?>
                                            <span class="input-group-addon"><i class="fa fa-twitter"></i></span>
                                        </div>
                                        <?php break;
                                        default:?>
                                        
                                            <?php echo form_input($field,$encuesta->{$field},'class="form-control"'); ?>
                                        
                                        <?php break;?>
                                    <?php }?>
                                </div>
                            <?php endforeach;?>
                            <?php foreach($asignacion->table_fields as $campo): ?>
                                <?php if($campo->tipo!='hidden'){?>
                                <label><?=$campo->nombre?></label>
                                <?php }?>
                                <?php switch($campo->tipo){
                                      case 'text':
                                      ?>
                                        <?=form_input($asignacion->table.'['.$campo->slug.']',$encuesta->{$asignacion->table}[$campo->slug],'class="form-control"')?>
                                    <?php break;
                                       case 'number':
                                           
                                    ?>  
                                     <?=form_input($asignacion->table.'['.$campo->slug.']',$encuesta->{$asignacion->table}[$campo->slug],'class="form-control" on-change="is_number(this)"')?>
                                    <?php break;
                                       case 'select':
                                           
                                    ?>
                                    <?=form_dropdown($asignacion->table.'['.$campo->slug.']',option_select($campo->opciones,'Seleccionar'),$encuesta->{$asignacion->table}[$campo->slug],'class="as-dark-background"')?>
                                    
                                    <?php break;
                                        case 'hidden':
                                    ?>
                                    <input type="hidden" name="<?=$asignacion->table.'['.$campo->slug.']'?>" value="<?=$encuesta->{$asignacion->table}[$campo->slug]?>" />
                                    <?php break;
                                       default:
                                    ?>
                                    <div class="alert alert-warning"><i class="fa fa-warning"></i> <?=lang('encuesta:error_input')?></div>
                                    <?php break;?>
                                    
                                <?php }?>
                            <?php endforeach;?>
                            <hr />
                            <div class="form-actions text-center">
                                <a class="btn btn-color-grey-light" href="<?=base_url($this->uri->uri_string())?>">Cancelar</a>
                                
                                <a class="btn" href="#" onclick="$('#tabs li:eq(1) a').tab('show')">Siguiente</a>
                                
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-form">
                            <?php $index = 1; ?>
                            <?php foreach($fields as $id_pregunta=>$field):?>
                                <div class="form-group">
                                    <label class="<?=form_error('pregunta['.$id_pregunta.']')?'text-danger':''?>"><?=$index?>.-<?=$field['titulo']?></label>
                                    
                                    <?php if ($field['muestra']):?>
                                    <div>
                                    <img src="<?=base_url('files/large/'.$field['muestra'])?>" />
                                    </div>
                                    <?php endif;?>
                                    <?php switch($field['tipo']){
                                        case 'radio': 
                                            echo '<div class="radio">';
                                            
                                            foreach($field['opciones'] as $opcion):
                                                echo '<label>'.$opcion['input'].$opcion['label'].'</label> ';
                                            endforeach;
                                            
                                            echo '</div>';
                                        break;
                                        
                                        case 'checkbox': 
                                            echo '<div class="checkbox">';
                                            
                                            foreach($field['opciones'] as $opcion):
                                                echo '<label>'.$opcion['input'].$opcion['label'].'</label> ';
                                            endforeach;
                                            
                                            echo '</div>';
                                        break;
                                        
                                        case 'text': 
                                           
                                            
                                            foreach($field['opciones'] as $opcion):
                                                echo $opcion['input'];
                                            endforeach;
                                            
                                           
                                        break;
                                    ?>
                                       
                                    <?php }?>
                                    
                                </div>
                                <?php $index++; ?>
                            <?php endforeach;?>
                            
                            <hr />
                            <div class="form-actions text-center">
                                <a class="btn btn-color-grey-light" onclick="$('#tabs li:eq(0) a').tab('show')" href="#">Regresar</a>
                                <button class="btn" name="btnAction" value="save" >Terminar  y enviar</button>
                            </div>
                        </div>
                        
                    </div>
                    
                    
                    <hr />
                    <p>Nota: Los campos marcados con (*) son obligatorios</p>
                <?php }else{?>
                   
                        
                        <div class="form-group">
                        
                            <?=form_input('auth','','class"form-control" placeholder="Ejemplo 15B19-0219"')?>
                            <p class="help-block"><?=$asignacion->placeholder?></p>
                        </div>
                        
                        <?php if($asignacion->aviso) {?>
                            
                            <div style="height: 200px; overflow-y: scroll;">
                                <h3 class="text-center">AVISO DE PRIVACIDAD</h3>
                                <?=$asignacion->aviso?>
                            </div>
                            <div class="form-group">
                                <label class="checkbox"><input type="checkbox" name="aviso" value="1" /> He leido y estoy de acuerdo con el Aviso de Privacidad de esta encuesta.</label>
                            </div>
                        <?php } ?>
                        
                        <button class="btn btn-success">Continuar</button>
                       
                    
                <?php }?>
                <?php echo form_close();?>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modalAviso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Aviso de privacidad</h4>
      </div>
      <div class="modal-body">
        <?=$asignacion->aviso?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        
      </div>
    </div>
  </div>
</div>