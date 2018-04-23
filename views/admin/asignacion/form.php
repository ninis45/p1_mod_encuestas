<section ng-controller="InputAsignacionCtrl">
    <div class="lead text-success"><?=lang('asignacion:'.$this->method)?></div>
    <?php echo form_open($this->uri->uri_string()); ?>
    <div class="form-group">
        <label>Titulo</label>
        <?=form_input('titulo',$asignacion->titulo,'class="form-control" ng-model="titulo" ng-init="titulo=\''.$asignacion->titulo.'\'"')?>
    </div>
    <div class="form-group">
        <label>Slug</label>
         <slug from="titulo" to="slug" >
        <?=form_input('slug',$asignacion->slug,'class="form-control" ng-model="slug" readonly')?>
        </slug>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group" ng-init="table.slug = '<?=$asignacion->table?>'">
                <label>Tabla o Módulo</label>
               <?=form_dropdown('table',array(''=>' [ Elegir ] '),null,'class="form-control" ng-options="module.name for module in modules track by module.slug" ng-model="table"  ng-change="select_index(table)" ')?>
               <input type="hidden" name="index_table" value="{{index_table}}" />
            </div>
            <div class="form-group">
                <label>Tiempo</label>
                <?=form_input('tiempo',$asignacion->tiempo,'class="form-control"')?>
                <p class="help-block">Establece en cuanto tiempo tendra que ser respondida la encuesta(Minutos)</p>
            </div>
            <div class="form-group" ng-init="auth_by='<?=$asignacion->auth_by?>'" >
                <label>Autenticado por</label>
                <select name="auth_by" class="form-control" ng-model="auth_by">
                    <option value="codigo">Código (Recomendado)</option>
                    <option ng-repeat="row in rows_left" value="{{row}}">{{row}}</option>
                </select>
               
            </div>
            
            <div class="form-group" ng-init="comodin='<?=$asignacion->comodin?>'" >
                <label>Comodin</label>
                <select name="comodin" class="form-control" ng-model="comodin">
                    <option value="">Ninguno</option>
                    <option ng-repeat="row in rows_left" value="{{row}}">{{row}}</option>
                </select>
                <p class="help-block">Permite realizar la busqueda más precisa en caso de duplicidad de búsqueda de claves.</p>
            </div>
        </div>
        <div class="col-md-6">
            
            <div class="form-group" ng-init="table_id='<?=$asignacion->table_id?>'">
                <label>Columna primaria</label>
               <?=form_dropdown('table_id',array(''=>' [ Elegir ] '),null,'class="form-control" ng-options="row for row in rows_left track by row" ng-model="table_id" ')?>
            </div>
            <div class="form-group" ng-init="group_by='<?=$asignacion->group_by?>'" >
                <label>Agrupado por</label>
               <?=form_dropdown('group_by',array(''=>' [ Elegir ] '),null,'class="form-control" ng-options="row for row in rows_left track by row" ng-model="group_by" ')?>
                <p class="help-block">Como deseas agrupar los registros en el panel de administración.</p>
            </div>
            
            <div class="form-group" >
                <label>Ayuda/Placeholder</label>
                <?=form_input('placeholder',$asignacion->placeholder,'class="form-control"')?>
            </div>
            <div class="form-group" >
                <label>Valor del comodín</label>
                <?=form_input('comodin_valor',$asignacion->comodin_valor,'class="form-control"')?>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <label>Instrucciones</label>
        <?=form_textarea('instrucciones',$asignacion->instrucciones,'class="form-control" ng-non-bindable')?>
        <p class="help-block">Deseas agregar unas instrucciones al formulario</p>
    </div>
    
    <div class="form-group">
        <label>Aviso de privacidad</label>
        <?=form_textarea('aviso',$asignacion->aviso,'class="form-control" ng-non-bindable')?>
        <p class="help-block">Agrega un aviso de privacidad antes de comenzar la encuesta.</p>
    </div>
    <hr />
    <div class="alert alert-info"><?=lang('asignacion:helper')?></div>
    <div class="form-group">
                        <label class="control-label">Campos:</label><br />
                        <?php $extra = array('edad'=>'Edad','sexo'=>'Sexo','email'=>'Correo electrónico','telefono'=>'Teléfono','social_facebook'=>'Facebook','social_twitter'=>'Twitter','social_instagram'=>'Instagram','social_whatsapp'=>'Whatsapp','social_otro'=>'Otra red social');?>
                        <?php foreach($extra as $field=>$label):?>
                        
                        <label class="checkbox-inline">
                            
                            <input type="checkbox" value="<?=$field?>" name="campos[]" <?=in_array($field,$campos)?'checked':''?>  <?=in_array($field,$cuestionario->campos)?'disabled':''?> />
                            <?=$label?> 
                       </label>
                       
                      
                    
                       <?php endforeach;?>
                       <label class="checkbox-inline">
                            
                            <input type="checkbox" disabled="" checked=""/>
                            IP
                       </label>
    </div>
    <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Columna</th>
                            <th width="10%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="row in rows_left">
                            <td>{{row}}</td>
                            <td>
                                <a href="#" class="btn btn-primary" ng-click="add_item(row,'table')"><i class="fa fa-arrow-right"></i></a>
                                <input type="hidden" name="rows[]" value="{{row}}" />
                            </td>
                        </tr>
                        
                        <tr ng-repeat="row in rows_extra">
                            <td>{{row}} <span class="text-muted">(extra)</span></td>
                            <td>
                                <a href="#" class="btn btn-primary" ng-click="add_item(row,'extra')"><i class="fa fa-arrow-right"></i></a>
                                <input type="hidden" name="rows[]" value="{{row}}" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Slug</th>
                            <th>Recurso</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="row in rows_right">
                            <td>
                                {{row.nombre}}
                                <input type="hidden" name="table_fields[{{$index}}][nombre]" value="{{row.nombre}}" />
                                <input type="hidden" name="table_fields[{{$index}}][slug]" value="{{row.slug}}" />
                                <input type="hidden" name="table_fields[{{$index}}][tipo]" value="{{row.tipo}}" />
                                <input type="hidden" name="table_fields[{{$index}}][opciones]" value="{{row.opciones}}" />
                                
                                <input type="hidden" name="table_fields[{{$index}}][resource]" value="{{row.resource}}" />
                            </td>
                            <td>{{row.slug}}</td>
                            <td>{{row.resource}}</td>
                            <td>
                                <a href="#" class="btn btn-danger" ng-click="remove_item($index,row.slug)"><i class="fa fa-remove"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="buttons divider clearfix" >
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )) ?>
         </div>
    <?php echo form_close();?> 
</section>

<script type="text/ng-template" id="ModalPrepend.html">
    <div class="modal-header">
                                <h3>Asignar campo</h3>
    </div>
    <div class="modal-body">
    <?php echo form_open();?>
       
        
        
        	       
                    <div class="form-group">
                        
                            <label>Nombre</label>
                            <input type="text" class="form-control" ng-model="form.nombre"/>
                                
                    </div>
                     <div class="form-group">
                        
                            <label>Slug</label>
                            <input type="text" class="form-control" ng-model="form.slug"/>
                                
                    </div>
                    
                    <div class="form-group">
                        
                            <label>Tipo</label>
                            <select class="form-control" ng-model="form.tipo">
                                <option value="text">Texto</option>
                                <option value="select">Seleccion</option>
                                <option value="upload">Archivo</option>
                                <option value="hidden">Oculto</option>
                            </select>
                                
                    </div>
                    
                    <div class="form-group" ng-if="form.tipo=='select'">
                        
                            <label>Opciones</label>
                            <textarea class="form-control" ng-model="form.opciones" placeholder="Ejemplo de sintaxis: 0=Inactivo|1=Activo"></textarea>
                                
                    </div>
                    
                
    <?php echo form_close();?>
    </div>
    <div class="modal-footer">
                                <button ui-wave class="btn btn-flat" ng-click="cancel()">Cancelar</button>
                                <button ui-wave class="btn btn-flat btn-primary"  ng-click="save_item()">Aceptar</button>
    </div>
</script>