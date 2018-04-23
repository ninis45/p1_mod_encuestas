<section class="" ng-controller="InputReport">
    <div class="lead text-success"><?=lang('encuesta:report')?></div>
    <div class="divider clearfix">
        
        
    </div>
   
   
    <div class="row">
        <?=form_open()?>
         
        <div class="col-md-3">
             <div class="checkbox">
                            <label >
                                <input type="checkbox" value="1" ng-model="check_all" />
                                SELECCIONAR TODOS
                            </label>
                            <a href="#" class="btn btn-mini btn-primary" ng-click="search()"><i class="fa fa-search"></i></a>
            </div>
            <hr />
             <div  ng-repeat="group in groups" class="checkbox">
                <label>
                                        <input  type="checkbox"  ng-model="group.checked"  value="{{group.name}}" ng-checked="check_all" />
                                        {{group.name}}
                </label>
            </div>
        </div>
        
        <div class="col-md-9">
              <div class="row">
                  <div class="col-md-6">
                       <div class="panel  panel-labeled " ng-if="status">
                            <div class="panel-body">
                                <div data-flot-chart 
                                data-data="donutChartHarmony.data"
                                data-options="donutChartHarmony.options"
                                style="width: 100%; height: 300px;"
                                ></div>
                                <span class="panel-label">Estadistica</span>
                            </div>
                        </div>   
                  </div>
                  
                  <div class="col-md-6">
                      <table class="table" ng-if="status">
                        <thead>
                            <tr>
                                <th>DESCRIPCIÓN</th>
                                <th class="text-center">CANTIDAD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Inactivos</td>
                                <td class="text-center">{{resumen.inactivos}}</td>
                            </tr>
                            <tr>
                                <td>Activos</td>
                                <td class="text-center">{{resumen.activos}}</td>
                            </tr>
                            
                            <tr>
                                <td>Total</td>
                                <td class="text-center">{{resumen.total}}</td>
                            </tr>
                        </tbody>
                      </table>
                      <div class="text-center" ng-if="status">
                        <a href="#" ng-click="download_chart()" class="btn btn-primary">Descargar datos</a>
                      </div>
                  </div>
              </div>
              <div ng-if="!disposed">Espere por favor...</div>
              <div ng-if="message" ng-bind-html="message"></div>
              <hr />
              
              <div class="item-pregunta" ng-repeat="(i,field) in fields">
                  <div class="row">
                  
                      <div class="col-md-7">
                            <div class="panel panel-labeled">
                                        <div class="panel-heading">{{field.label}}</div>
                                        <div class="panel-body">
                                            <div data-flot-chart
                                            data-data="field.data"
                                            data-options="field.options"
                                            style="width: 100%; height: 200px;"
                                            ></div>
                                            
                                        </div>
                            </div>  
                      
                      </div>
                      <div class="col-md-5">
                        <table class="table" >
                            <thead>
                                <tr>
                                    <th>DESCRIPCIÓN</th>
                                    <th class="text-center">CANTIDAD</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(j,label) in field.options.xaxis.ticks">
                                    
                                    <td>{{label[1]}}</td>
                                    <td class="text-center">{{fields[i].data[0].data[j][1]}}</td>
                                </tr>
                                
                            </tbody>
                          </table>
                      
                      </div>
                  </div>
              
              </div>
               
        </div>
        <?=form_close()?>
    </div>
</section>