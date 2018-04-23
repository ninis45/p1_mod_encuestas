(function () {
    'use strict';
    
    angular.module('app.encuestas')
    .controller('InputCtrl',['$scope','$http','$rootScope','$filter','logger',InputCtrl])
    .controller('IndexCtrl',['$scope','$uibModal','$filter',IndexCtrl])
    .controller('InputReport',['$scope','$http','$timeout',InputReport])
    .controller('InputModalUpload',['$scope','$http','$timeout','$cookies','Upload',InputModalUpload])
    .controller('InputAsignacionCtrl',['$scope','$http','$uibModal',InputAsignacionCtrl])
    .controller('InputModal',['$scope','$http','$uibModalInstance','resource','field','rows_right','rows_left',InputModal]);;
    
    function InputModalUpload($scope,$http,$timeout,$cookies,Upload)
    {
        $scope.dispose = true;
        $scope.upload_file = function(file,cuestionario,asignacion)
        {
            $scope.dispose = false;
            
            file.upload = Upload.upload({
              url: SITE_URL+'admin/encuestas/import',
              data: { file:file,cuestionario:cuestionario,asignacion:asignacion,csrf_hash_name:$cookies.get(pyro.csrf_cookie_name)},
            });
            
            file.upload.then(function (response) {
              var  result = response.data,
                   data   = response.data.data;
              $timeout(function () {
                  file.result = response.data;
                  $scope.dispose = true;
                  
                  if(typeof item == 'undefined' || !item)
                  {
                      //item = {id:data.id_factura,xml:'',pdf:'',total:0,messages:[]};
                  }
                  $scope.status  = result.status;
                  $scope.message = result.message;
                 // if(type == 'xml' )
                  //{
                      //item['total']    = data.total;
                      //item['messages'] = result.message;
                  //}
                  
                  //$scope.id_factura = response.data.data.id_factura;
                  //item[type] = data.id;
                 
                 
              });
            }, function (response) {
              if (response.status > 0)
                $scope.errorMsg = response.status + ': ' + response.data;
            }, function (evt) {
              
              file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
            });
        }
    }
    function InputReport($scope,$http,$timeout)
    {
        $scope.check_all   = true;
        $scope.groups      = groups;
        $scope.groups_init = [];
        $scope.fields      = [];
        $scope.resumen     ={inactivos:0,total:0,inactivos:0};
        $scope.donutChartHarmony = {};
        $scope.status = false;
        $scope.disposed = true;
       
        
        
        var barChart = {};
        $scope.download_chart = function()
        {
             var send_data='id_cuestionario='+id_cuestionario+'&id_asignacion='+id_asignacion;
             $.each($scope.groups,function(index,item){
                    if(item.checked)
                    {
                        send_data+='&groups[]='+item.name;
                        //groups.push(item.name);
                    }
                    
             });
             
             window.open(SITE_URL+'admin/encuestas/chart/1?'+send_data);
        }
        $scope.search = function()
        {
            $scope.disposed = false;
            $scope.status = false;
            $scope.message = '';
            var send_data = [];
            $scope.fields =[];
            $.each($scope.groups,function(index,item){
                    if(item.checked)
                    {
                        send_data.push(item.name);
                    }
                    
            });
            $http.get(SITE_URL+'admin/encuestas/chart',{params:{'groups[]':send_data,id_cuestionario:id_cuestionario,id_asignacion:id_asignacion}}).then(function(response){
                    var result = response.data,
                          data = result.data;
                     
                     
                          
                          
                    
                    $scope.donutChartHarmony.data = [];
                    
                    $scope.donutChartHarmony.options = {
                        series: {
                            pie: {
                                show: true,
                                innerRadius: 0.55
                            }
                        },
                        legend: {
                            show: false
                        },
                        grid: {
                            hoverable: true,
                            clickable: true
                        },
                        colors: ["#1BB7A0", "#39B5B9", "#52A3BB", "#619CC4", "#6D90C5"],
                        tooltip: true,
                        tooltipOpts: {
                            content: "%p.0%, %s",
                            defaultTheme: false
                        }
                    };  
                    $scope.disposed = true;
                    $scope.message = result.message;
                    if(result.status == false) return false;
                    
                    $scope.status = true;
                    
                     $.each(data.preguntas,function(i,item){
                       
                        
                        var main_data = [
                            {
                                label: "Value A",
                                data: [],//barChart.data1,
                                bars: {
                                    order: 0
                                }
                            }
                        ];
                        var options = {
                            series: {
                                stack: true,
                                bars: {
                                    show: true,
                                    fill: 1,
                                    barWidth: 0.4,
                                    align: "center",
                                    horizontal: false
                                }
                            },
                            grid: {
                                hoverable: true,
                                borderWidth: 1,
                                borderColor: "#eeeeee"
                            },
                            
                            xaxis: {
                                    ticks: []
                                    
                                },
                            tooltip: true,
                            tooltipOpts: {
                                defaultTheme: false
                            },
                            colors: [ $scope.color.success]
                        };
                        $.each(item.opciones,function(j,opcion){
                            //console.log(j);
                           
                            options.xaxis.ticks.push([j,opcion.label]);
                            main_data[0].data.push([j,opcion.cantidad]);
                        });
                        
                        
                       
                        
                        $scope.fields.push({label:item.titulo,options:options,data:main_data});
                      
                        
                    });
                    
                    $scope.donutChartHarmony.data = [
                        {
                            label: "Inactivos",
                            data: data.inactivos
                        }, {
                            label: "Activos",
                            data: data.total - data.inactivos
                        }
                        ];
                        
                    $scope.resumen.inactivos = data.inactivos;
                    $scope.resumen.total     = data.total;
                    $scope.resumen.activos   = data.total - data.inactivos;
                   
                    
            });
        }
        
        
            $scope.$watch('check_all',function(newValue,oldValue){
                
                $.each($scope.groups,function(index,item){
                    
                    $scope.groups[index].checked = newValue;
                });
            })
            $scope.$watch('resumen',function(newValue,oldValue){
                
                console.log(newValue);
            },true);
            $scope.$watch('groups',function(newValue,oldValue){
                return false;
                if(!newValue) return false;
                $scope.status = false;
                var send_data = [];
                $scope.fields =[];
                $scope.resumen     ={inactivos:0,total:0,inactivos:0};
                
                $.each(newValue,function(index,item){
                    if(item.checked)
                    {
                        send_data.push(item.name);
                    }
                    
                });
                
                      
                $http.get(SITE_URL+'admin/encuestas/chart',{params:{'groups[]':send_data,id_cuestionario:id_cuestionario,id_asignacion:id_asignacion}}).then(function(response){
                    
                    var result = response.data,
                          data = result.data;
                     
                     
                          
                          
                    
                    $scope.donutChartHarmony.data = [];
                    
                    $scope.donutChartHarmony.options = {
                        series: {
                            pie: {
                                show: true,
                                innerRadius: 0.55
                            }
                        },
                        legend: {
                            show: false
                        },
                        grid: {
                            hoverable: true,
                            clickable: true
                        },
                        colors: ["#1BB7A0", "#39B5B9", "#52A3BB", "#619CC4", "#6D90C5"],
                        tooltip: true,
                        tooltipOpts: {
                            content: "%p.0%, %s",
                            defaultTheme: false
                        }
                    };  
                    if(result.status == false) return false;
                    
                    $scope.status = true;
                   
                    
                    $.each(data.preguntas,function(i,item){
                       
                        
                        var main_data = [
                            {
                                label: "Value A",
                                data: [],//barChart.data1,
                                bars: {
                                    order: 0
                                }
                            }
                        ];
                        var options = {
                            series: {
                                stack: true,
                                bars: {
                                    show: true,
                                    fill: 1,
                                    barWidth: 0.4,
                                    align: "center",
                                    horizontal: false
                                }
                            },
                            grid: {
                                hoverable: true,
                                borderWidth: 1,
                                borderColor: "#eeeeee"
                            },
                            
                            xaxis: {
                                    ticks: []
                                    
                                },
                            tooltip: true,
                            tooltipOpts: {
                                defaultTheme: false
                            },
                            colors: [ $scope.color.success]
                        };
                        $.each(item.opciones,function(j,opcion){
                            //console.log(j);
                           
                            options.xaxis.ticks.push([j,opcion.label]);
                            main_data[0].data.push([j,opcion.cantidad]);
                        });
                        
                        $scope.resumen.inactivos = data.inactivos;
                        $scope.resumen.total     = data.total;
                        $scope.resumen.activos   = data.total - data.inactivos;
                       
                        
                        $scope.fields.push({label:item.titulo,options:options,data:main_data});
                      
                        
                    });
                    
                    
                    $scope.donutChartHarmony.data = [
                        {
                            label: "Inactivos",
                            data: data.inactivos
                        }, {
                            label: "Activos",
                            data: data.total - data.inactivos
                        }
                        ];
                   
                });
            },true);
            
           
            
    }
    function IndexCtrl($scope,$uibModal,$filter)
    {
        //var init;
        $scope.dispose = true;
        $scope.encuestas = encuestas;
        $scope.filteredEncuestas = [];
        $scope.numPerPageOpt = [3, 5, 10, 20];
        $scope.numPerPage = $scope.numPerPageOpt[2];
        $scope.currentPage = 1;
        $scope.row = '';
        $scope.select = select;
        $scope.onFilterChange = onFilterChange;
        $scope.onNumPerPageChange = onNumPerPageChange;
        $scope.onOrderChange = onOrderChange;
        $scope.search = search;
        $scope.order  = order;
        init();
        console.log($scope.encuestas);
        function onNumPerPageChange() {
            $scope.select(1);
            return $scope.currentPage = 1;
        };

        function onOrderChange() {
            $scope.select(1);
            return $scope.currentPage = 1;
        };
         function search() {
            $scope.filteredEncuestas = $filter('filter')($scope.encuestas, $scope.searchKeywords);
            
            
            
            return $scope.onFilterChange();
        };
        function order(rowName) {
            if ($scope.row === rowName) {
            return;
            }
            $scope.row = rowName;
            $scope.filteredEncuestas = $filter('orderBy')($scope.encuestas, rowName);
            return $scope.onOrderChange();
        };
        function select(page) {
            
            
            var end, start;
            start = (page - 1) * $scope.numPerPage;
            end = start + $scope.numPerPage;
            
            console.log(start);
            console.log(end);
            return $scope.currentPageEncuesta = $scope.filteredEncuestas.slice(start, end);
        };
        function onFilterChange() {
            $scope.select(1);
            $scope.currentPage = 1;
            return $scope.row = '';
        };
        function init() {
            
            $scope.search();
            return $scope.select($scope.currentPage);
        };
        $scope.open_upload = function()
        {
             var modalInstance = $uibModal.open({
                            animation: true,
                            templateUrl: 'modalUpload.html',
                            controller: 'InputModalUpload',
                            //size: size,
                            resolve: {
                                /*resource:function()
                                {
                                    return resource;
                                },
                                field:function()
                                {
                                    return item;
                                },
                                rows_right:function()
                                {
                                    return $scope.rows_right;
                                },
                                rows_left: function () {
                                    return $scope.rows_left;
                                }*/
                               
                               
                                
                                
                            }
            });
        }
    }
    function InputCtrl($scope,$http,$rootScope,$filter,logger)
    {
        var init;
        $scope.group = group;
        $scope.stores = [];
        $scope.searchKeywords = '';
        $scope.filteredStores = [];
        $scope.row = '';
        $scope.select = select;
        $scope.onFilterChange = onFilterChange;
        $scope.onNumPerPageChange = onNumPerPageChange;
        $scope.onOrderChange = onOrderChange;
        $scope.search = search;
        $scope.order = order;
        $scope.numPerPageOpt = [3, 5, 10, 20];
        $scope.numPerPage = $scope.numPerPageOpt[2];
        $scope.currentPage = 1;
        $scope.currentPage = [];

       

        function select(page) {
            
            var end, start;
            start = (page - 1) * $scope.numPerPage;
            end = start + $scope.numPerPage;
            return $scope.currentPageStores = $scope.filteredStores.slice(start, end);
        };

        function onFilterChange() {
            $scope.select(1);
            $scope.currentPage = 1;
            return $scope.row = '';
        };

        function onNumPerPageChange() {
            $scope.select(1);
            return $scope.currentPage = 1;
        };

        function onOrderChange() {
            $scope.select(1);
            return $scope.currentPage = 1;
        };

        function search() {
            $scope.filteredStores = $filter('filter')($scope.stores, $scope.searchKeywords);
            
            
            console.log($scope.searchKeywords);
            return $scope.onFilterChange();
        };

        function order(rowName) {
            if ($scope.row === rowName) {
            return;
            }
            $scope.row = rowName;
            $scope.filteredStores = $filter('orderBy')($scope.stores, rowName);
            return $scope.onOrderChange();
        };

        init = function() {
            
            $scope.search();
            return $scope.select($scope.currentPage);
        };

      
        
        $scope.show_listing = function(group)
        {
            $scope.group = group;
        }
        $scope.$watch('group',function(newValue,oldValue){
            
           
            if(!newValue) return false;
            
            $rootScope.$broadcast('preloader:active');
            $scope.encuestas = [];
            $scope.currentPageStores = [];
            $http.get(SITE_URL+'admin/encuestas/listing/'+$scope.id_cuestionario+'/'+$scope.id_asignacion,{params:{group:newValue}}).then(function(response){
                
                var request = response.data,
                       data = request.data;
                       
                $scope.message = request.message;
                $scope.encuestas = data;
                
                $rootScope.$broadcast('preloader:hide');
                $scope.stores = data;
                init();
            
            });
        },true);
        
        
        
        
    }
    function InputModal($scope,$http,$uibModalInstance,resource,field,rows_right,rows_left)
    {
        
        
        $scope.form = {};
        //field.visible  = false;
        $scope.form.slug = field;
        $scope.form.resource = resource;
        
        $scope.save_item = function()
        {
           
            rows_right.push($scope.form);
            
            $scope.form = {};
            $uibModalInstance.close();
        }
        
        $scope.cancel = function() {
                
                $uibModalInstance.close();
        }
    }
    function InputAsignacionCtrl($scope,$http,$uibModal)
    {
        $scope.modules    = modules?modules:[];
        $scope.rows_right = rows_right?rows_right:[];
        $scope.rows_extra = [];//['email','sexo','telefono','edad','social_facebook','social_twitter','social_otro'];
        $scope.table = {
            
           
        };
        
        console.log($scope.rows_right);
        $scope.select_index = function(module)
        {
            //console.log($scope.modules);
            //var index = $scope.modules.indexOf({slug:module.slug});
            //console.log(index);
            
            $.each($scope.modules,function(index,value){
                
                if(value.slug == module.slug)
                {
                    $scope.index_table = index;
                    return false;
                }
                
            });
        }
        $scope.$watch('table',function(newValue,oldValue){
            
             //console.log(newValue);
             //console.log(oldValue);
           
            
            if(!newValue){
                $scope.rows_left = []
                
                return false;    
            }
            
            
            $.each($scope.modules,function(index,value){
                
                if(newValue.slug == value.slug)
                {
                    $scope.index_table = index;
                    $scope.table       = $scope.modules[index];
                }
                
            });
            $scope.rows_left = newValue?newValue.rows:[];
        });
        $scope.remove_item = function(index)
        {
            $scope.rows_right.splice(index,1);
        }
        $scope.add_item = function(item,resource)
        {
            var modalInstance = $uibModal.open({
                            animation: true,
                            templateUrl: 'ModalPrepend.html',
                            controller: 'InputModal',
                            //size: size,
                            resolve: {
                                resource:function()
                                {
                                    return resource;
                                },
                                field:function()
                                {
                                    return item;
                                },
                                rows_right:function()
                                {
                                    return $scope.rows_right;
                                },
                                rows_left: function () {
                                    return $scope.rows_left;
                                }
                               
                               
                                
                                
                            }
            });
        }
        
    }
    
})();