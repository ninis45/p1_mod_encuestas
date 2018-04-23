<section>
    <div class="lead text-success"><?=sprintf(lang('encuesta:'.$this->method),$this->method=='edit'?$encuesta->id:'NA')?></div>
    <?php echo form_open();?>
    
     <div class="ui-tab-container ui-tab-horizontal">
      
        
    	<uib-tabset justified="false" class="ui-tab">
            <uib-tab heading="Datos generales">
                 <?php $campos = array('edad'=>'Edad','sexo'=>'Sexo','email'=>'Correo electrónico','telefono'=>'Teléfono','social_facebook'=>'Facebook','social_twitter'=>'Twitter','social_instagram'=>'Instagram','social_whatsapp'=>'Whatsapp','social_otro'=>'Otra red social');?>
                <?php foreach($campos as $field=>$label):?>
                                <?php if(in_array($field,$cuestionario->campos)==false) continue;?>
                                <div class="form-group">
                                    <label><?=$label?></label>
                                    <?php switch($field){
                                        case 'sexo':
                                        ?>
                                            <?php echo form_dropdown($field,array(''=>'Elegir','1'=>'Hombre','2'=>'Mujer'),$encuesta->{$field},'class="form-control"') ?>
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
                
               <div class="form-group">
                    <label>Estatus</label>
                    <?php echo form_dropdown('activo',array('1'=>'Si','0'=>'No'),$encuesta->activo,'class="form-control"') ?>
                    <p class="help-block">Determina el estatus de la encuesta</p>
                </div>
                
            </uib-tab>
            <uib-tab heading="Cuestionario">
                <?php $index = 1; ?>
                 <?php foreach($fields as $id_pregunta=>$field):?>
                    <div class="form-group">
                        <label><?=$index?>.-<?=$field['titulo']?></label>
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
                    <?php $index++;?>
                <?php endforeach;?>
            </uib-tab>
        </uib-tabset>
     </div>
   
    
    <div class="buttons divider clearfix" >
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )) ?>
   </div>
    <?php echo form_close() ?>
</section>