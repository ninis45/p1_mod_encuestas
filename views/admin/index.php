<section ng-controller="IndexCtrl" >
    <div class="lead text-success"><?=lang('encuesta:title')?></div>
    
    <?php echo form_open($this->uri->uri_string() ,'class="form-inline" method="get" id="form_search" ') ?>
        <div class="row">
    		    <?php if($groups){ ?>
                
                <div class="form-group col-md-3">
                    <label>Grupo</label>
                    <?=form_dropdown('group',array(''=>'[ SELECCIONAR GRUPO ]')+$groups,$this->input->get('group'),'class="form-control" style="width:100%;" onchange="$(\'#form_search\').submit();" ');?>
                </div>
                <?php }?>
                <div class="form-group col-md-2">
                    <label>Estatus</label>
                    <?=form_dropdown('activo',array(''=>'Mostrar todos','0'=>'Inactivos','1'=>'Activos'),$this->input->get('activo'),'class="form-control" onchange="$(\'#form_search\').submit();"');?>
                </div>
                <div class="form-group col-md-4">
                        <label>Por palabra</label>
                        <input type="text"
                         placeholder="Buscar..."
                         class="form-control"
                         data-ng-model="searchKeywords"
                         data-ng-keyup="search()" style="width: 100%;"/>
                </div>
                <div class="col-md-3">
               	    
                    
                          <a href="<?=base_url('admin/encuestas/reporte/'.$id_cuestionario.'/'.$asignacion->id)?>" class="btn btn-default"><i class="fa fa-area-chart"></i> Reportes</a>
                          <div class="btn-group" uib-dropdown is-open="status.isopen1">
                                <button  type="button" class="btn btn-primary  dropdown-toggle" uib-dropdown-toggle ng-disabled="disabled"> Acciones <span class="caret"></span> </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="<?=base_url('admin/encuestas/export/'.$id_cuestionario.($asignacion?'/'.$asignacion->id:'').'?'.http_build_query($_GET))?>" target="_blank" ng-if="encuestas.length>0" ><i class="fa fa-download"></i> Exportar csv</a></li>
                                    <li><a href="#" ng-click="open_upload()"  ><i class="fa fa-upload"></i> Importar csv</a></li>
                                    
                                </ul>
                            </div>
                </div>
        </div>
        <hr />
    	<div class="alert alert-info" ng-if="message" ng-bind-html="message"></div>
        <div class="divider clearfix">
                    
            <span class="pull-right">Total registros: {{encuestas.length}}</span>
                 
        </div>
        <?php
     
            if($count_resources>count($encuestas))
            {
                echo '<div class="alert alert-info">'.sprintf(lang('encuesta:partial_error'),base_url('admin/encuestas/load/'.$id_cuestionario.'/'.$asignacion->id.'?generar=1&group='.$this->input->get('group'))).'</div>';
            }
        ?>
        <table class="table" ng-if="currentPageEncuesta.length>0">
                    <thead>
                        <tr>
                            <th width="10%">#ID</th>
                            <th>Auth</th>
                            <th><?=ucfirst($asignacion->table_id)?></th>
                            <th>LLenado</th>
                            
                            <th width="16%"></th>
                        </tr>
                    </thead>
                    <tbody>
                   
                        <tr   ng-repeat="encuesta in currentPageEncuesta " ng-class="{'danger':encuesta.activo==0}">
                            <td>{{encuesta.id}}</td>
                            <td>{{encuesta.auth_by}}</td>
                            <td>{{encuesta.table_id}}</td>
                            <td>
                                <a ng-if="encuesta.link" target="_blank" href="{{encuesta.link}}" title="Ver llenado en línea">En línea</a>
                                <i class="fa fa-warning text-danger" ng-if="!encuesta.link"></i>
                            </td>
                            <td>
                                <?php ///echo anchor('admin/encuestas/delete/{{encuesta.id}}', '<i class="fa fa-trash"></i>', 'class="btn-icon btn-icon-sm btn-danger" confirm-action ui-wave') ?> 
                                <?php echo anchor('admin/encuestas/edit/{{encuesta.id}}', '<i class="fa fa-edit"></i>', 'class="btn-icon btn-icon-sm btn-primary" ui-wave') ?>
                                
                            </td>
                        </tr>
                   
                    </tbody>
        </table>
        <div ng-if="currentPageEncuesta.length>0">
                    <uib-pagination class="pagination-sm"
                    ng-model="currentPage"
                    total-items="filteredEncuestas.length"
                    max-size="6"
                    ng-change="select(currentPage)"
                    items-per-page="numPerPage"
                    rotate="false"
                    previous-text="&lsaquo;" next-text="&rsaquo;"
                    boundary-links="true"></uib-pagination>
        </div>	
    <?php echo form_close();?>
</section>
<script type="text/ng-template" id="modalUpload.html">
                            <div class="modal-header">
                                <h3>Subir archivo csv</h3>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Buscar archivo</label>
                                    <input type="file" accept=".csv" ng-disabled="!dispose" ngf-select="upload_file(file_csv,<?=$id_cuestionario?>,<?=$asignacion->id?>)" ng-model="file_csv" name="file_csv" ngf-model-invalid="errorFile"/>
                                    <md-progress-linear md-mode="determinate" ng-show="!dispose" value="{{file_csv.progress}}"></md-progress-linear>
                                    <div class="alert" ng-class="{'alert-danger':status==false}" ng-if="message" ng-bind-html="message"><div>
                                
                                </div>
                               
                            </body>
</script>