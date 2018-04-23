<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends Admin_Controller {
	protected $section='encuestas';
	public function __construct()
	{
		parent::__construct();
        $this->lang->load('encuesta');
        $this->load->library('encuesta');
        $this->load->model(array(
            'cuestionarios/cuestionario_m',
            'asignacion_m',
            'encuesta_m',
            'centros/centro_m'
        ));
        $this->config->load('files/files');
        $this->_path = FCPATH.rtrim($this->config->item('files:path'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        $this->validation_rules = array(
				     array(
                        'field' => 'activo',
						'label' => 'Activo',
						'rules' => 'trim|required'
                     ),
                     
					 'edad'=>array(
						'field' => 'edad',
						'label' => 'Edad',
						'rules' => 'trim|integer'
						),
                     'sexo'=>array(
						'field' => 'sexo',
						'label' => 'Sexo',
						'rules' => 'trim'
						),
                    'email'=>array(
						'field' => 'email',
						'label' => 'Correo electrónico',
						'rules' => 'trim|valid_email'
						),   
                    'social_facebook'=>array(
						'field' => 'social_facebook',
						'label' => 'Facebook',
						'rules' => 'trim'
						),  
                    'social_twitter'=>array(
						'field' => 'social_twitter',
						'label' => 'Twitter',
						'rules' => 'trim'
						), 
                    'social_whatsapp'=>array(
						'field' => 'social_whatsapp',
						'label' => 'Instagram',
						'rules' => 'trim'
						),   
                    'social_otro'=>array(
						'field' => 'social_otro',
						'label' => 'Otra red social',
						'rules' => 'trim'
						),                       
          );
    }
    
    function index()
    {
        $cuestionarios = $this->cuestionario_m->get_all();
        $asignaciones  = $this->asignacion_m->get_all();
        
        
        $this->template->title($this->module_details['name'])
			->set('asignaciones',$asignaciones)
			->set('cuestionarios',$cuestionarios)
            
			->build('admin/init');
    }
    function delete()
    {
        
    }
    function listing($id_cuestionario=false,$id_asignacion=false)
    {
        set_time_limit(0);
        $group         = $this->input->get('group');
        $asignacion    = false;
        $response      = array(
            'status'  => false,
            'message' => '',
            'data'    => array()
        
        );
        
        
        $base_where = array(
            'id_cuestionario' => $id_cuestionario
        );
        
        //$this->encuesta_m->where($base_where);
        
        if($id_asignacion)
        {
            $base_where['id_asignacion'] = $id_asignacion;
            
            $asignacion                  = $this->db->where('id',$id_asignacion)
                                                    ->get('encuesta_asignaciones')->row();
            
                                            
            if($group)
            {
                
                
                $base_where[$asignacion->group_by] = $group;
                $resource = $this->db->where($asignacion->group_by,$group)->count_all_results($asignacion->table);
            }
            
            
            $this->encuesta_m->where($base_where);
            $this->encuesta_m->join($asignacion->table,'encuestas.table_id= CONVERT(default_'.$asignacion->table.'.'.$asignacion->table_id.' using utf8) collate utf8_spanish_ci');
           
        }
        else
        {
            $this->encuesta_m->where($base_where);
        }
        
        
        $response['data'] = $this->encuesta_m->get_all();
        //print_r($response['data']);
        if($asignacion)
        {
            foreach($response['data'] as &$encuesta)
            {
                if($asignacion->auth_by)
                {
                    $encuesta->auth_by = $encuesta->{$asignacion->auth_by};
                }
                $encuesta->link = base_url('encuestas/llenado/'.$asignacion->slug.'?auth='.$encuesta->{$asignacion->auth_by});
            }
        }
        
        
        if($response['data'])
        {
            $response['status'] = true;
        }
        
        if($group)
        {
            if($resource>count($response['data']))
            {
                $response['message'] = sprintf(lang('encuesta:partial_error'),base_url('admin/encuestas/load/'.$id_cuestionario.'/'.$id_asignacion.'?generar=1&group='.$group));
            }
        }
                            
        echo json_encode($response);
        exit();
    }
    function load($id_cuestionario=0,$id_asignacion=0)
    {
       
        set_time_limit(0);
        $f_keywords = $this->input->get('f_keywords');
        $group      = $this->input->get('group');
        $activo     = $this->input->get('activo');
        
        $group_init = '';
        $groups     = array();
        
        $encuestas  = false;
        $asignacion = false;
        $count_resources = 0;
        $base_where = array(
            'id_cuestionario' => $id_cuestionario,
            //'tipo_esc' => 'plantel'
        );
        
        if(is_numeric($activo))
        {
            $base_where['activo'] = $activo;
        }
        
        if($id_asignacion)
        {
            $base_where['id_asignacion'] = $id_asignacion;
            $asignacion = $this->asignacion_m->get($id_asignacion);
            
            
            if($asignacion->group_by){
                
                $groups = $this->db->order_by($asignacion->group_by)
                                ->get($asignacion->table)
                                ->result();
                
                if($group)
                {
                    $result_group = $this->db->where($asignacion->group_by,$group)
                                            ->get($asignacion->table)->result();
                    
                    $tables_ids = array_for_select($result_group,$asignacion->table_id,$asignacion->table_id);
                    
                    
                     $this->encuesta_m->where_in('table_id',$tables_ids);
                     
                     $encuestas = $this->encuesta_m->where($base_where)
                                    ->get_all();
                            
                     if(is_numeric($activo) == false)      
                        $count_resources =  $this->db->where($asignacion->group_by,$group)->count_all_results($asignacion->table);
                                    
                    
                }
                if($groups)
                {
                    $groups = array_for_select($groups,$asignacion->group_by,$asignacion->group_by);
                    
                    
                }
                
            }
            
            
            
             
        }
        else
        {
            $base_where['id_asignacion IS NULL'] = null;
            $encuestas = $this->encuesta_m->where($base_where)
                                    ->get_all();
        }
        
        
        
        
        $generar    = $this->input->get('generar');
        
        
        
       
        if($generar)
        {
            
            Encuesta::Generate($asignacion,$this->input->get('group'));
            
            
            redirect('admin/encuestas/load/'.$id_cuestionario.'/'.$id_asignacion.'?group='.$this->input->get('group'));
        }
        
        
        
        if($encuestas)
        {
            foreach($encuestas as &$encuesta)
            {
                            $encuesta->extra = $this->db->where($asignacion->table_id,$encuesta->table_id)->get($asignacion->table)->row();
                           
                            if($asignacion->auth_by == 'codigo')
                            {
                                $encuesta->link = base_url('encuestas/llenado/'.$asignacion->slug.'?auth='.$encuesta->codigo);
                            }
                            else
                            {
                                $encuesta->link = $encuesta->extra?base_url('encuestas/llenado/'.$asignacion->slug.'?auth='.$encuesta->extra->{$asignacion->auth_by}):'';
                            }
                            
                            if($asignacion->aviso && $encuesta->link)
                            {
                                $encuesta->link.='&aviso=1';
                            }
            }
        }
       
        
        
       
       
        
        $this->template->title($this->module_details['name'])
		    ->append_js('module::encuesta.controller.js')
            //->enable_parser(true)
            ->append_metadata('<script type="text/javascript">var encuestas='.($encuestas?json_encode($encuestas):'[]').';</script>')
           
            ->set('count_resources',$count_resources)
            
            ->set('groups',$groups)
            ->set('encuestas',$encuestas)
            ->set('id_cuestionario',$id_cuestionario)
            ->set('asignacion',$asignacion)
            //->set('centros',$this->centro_m->dropdown('id','nombre'))
			->build('admin/index');
    }
 
    function cuestionario($id='')
    {
        $base_where = array(
        
            'id_cuestionario' => $id,
            'id_asignacion IS NULL' => NULL
        );
        $encuestas = $this->encuesta_m->where($base_where)
                                ->get_all();
        
        $this->template->title($this->module_details['name'])
		  
            ->enable_parser(true)
            ->set('id',$id)
            ->set('tipo','cuestionario')
            ->set('encuestas',$encuestas)
			->build('admin/index');
    }
    
    function edit($id)
    {
        $encuesta     = $this->encuesta_m->get_result($id) or redirect('admin');
        $asignacion   = false;
        $cuestionario = $this->cuestionario_m->get_cuestionario($encuesta->id_cuestionario) OR show_error('El cuestionario no existe o fue removida');
        
        
        
        if($encuesta->id_asignacion)
        {
            $asignacion    = $this->asignacion_m->get_asignacion($encuesta->id_asignacion) OR show_error('La configuracion no existe o fue removida');
            //print_r($asignacion);
            //exit();
            
            //$asignacion->table_fields = $asignacion->table_fields?json_decode($asignacion->table_fields):array();
           
            $cuestionario->campos = array_merge($cuestionario->campos,$asignacion->campos?$asignacion->campos:array());
            
            
            
        }
        $encuesta->{$asignacion->table} = (array)$this->db->where($asignacion->table_id,$encuesta->table_id)
                                        ->get($asignacion->table)->row();
                                        
        $this->form_validation->set_rules($this->validation_rules);
        
        if($this->form_validation->run())
        {
            $data  = array(
                'updated_on' => now(),
                'ip'         => $this->input->ip_address(),
                'activo'     => $this->input->post('activo')
            );
           
            
            foreach($cuestionario->campos as $campo)
            {
                $data[$campo] = $this->input->post($campo)?$this->input->post($campo):null;
            }
            
            if($this->encuesta_m->update($encuesta->id,$data))
            {
                Encuesta::SaveResource($asignacion,$encuesta->table_id,$this->input->post($asignacion->table));
                Encuesta::SaveValues($encuesta->id,$this->input->post('pregunta'));
                $this->session->set_flashdata('success',lang('global:save_success'));
            }
            else
            {
                $this->session->set_flashdata('error',lang('encuesta:save_error'));
            }
            redirect('admin/encuestas/edit/'.$id);
        }
        elseif($_POST)
        {
            $encuesta = (Object)$_POST;
        }
        ///Asignamos las respuestas de las preguntas dinamicas
        $fields = $this->encuesta->set_values($encuesta->pregunta)
                            ->build_form($cuestionario->id); 
                            
      
        $this->template->title($this->module_details['name'])
			//->set('items',$this->cuestionario_m->get_all())
			 ->set('fields',$fields)
            ->enable_parser(true)
            ->set('tipo','cuestionario')
            ->set('id',$id)
            ->set('cuestionario',$cuestionario)
            ->set('encuesta',$encuesta)
            ->set('asignacion',$asignacion)
			->build('admin/form');
    }
    function create($id_cuestionario=0)
    {
        $encuesta = new StdClass();
        $cuestionario = $this->cuestionario_m->get_cuestionario($id_cuestionario) OR redirect('admin/encuestas');
        
        if($_POST)
        {
            $data = array(
                'id_cuestionario' => $id,
                'ip'              => $this->input->ip_address(),
                'created_on'      => now()
                
            );
            if($id_encuesta = $this->encuesta_m->insert($data))
            {
                Encuesta::SaveValues($id_encuesta,$this->input->post('pregunta'));
                $this->session->set_flashdata('success',lang('global:save_success'));
            }
            else
            {
                $this->session->set_flashdata('error',lang('global:save_error'));
            }
            
            
			redirect('admin/encuestas/'.$tipo.'/'.$id);
        }
        
        foreach ($this->validation_rules as $rule)
		{
			$encuesta->{$rule['field']} = $this->input->post($rule['field']);
		}
        /*switch($tipo){
            case 'cuestionario':
                $id_cuestionario = $id;
            break;
        }*/
        
        $fields = $this->encuesta->build_form($id_cuestionario); 
    
        //print_r($fields);
        $this->template->title($this->module_details['name'])
			//->set('items',$this->cuestionario_m->get_all())
			 ->set('fields',$fields)
             ->set('cuestionario',$cuestionario)
            ->enable_parser(true)
            ->set('tipo','cuestionario')
            //->set('id',$id)
            ->set('encuesta',$encuesta)
			->build('admin/form');
    }
    function reporte($id_cuestionario=0,$id_asignacion=0,$group='')
    {
        $asignacion = false;
        $groups     = array();
        
        if($id_asignacion)
        {
            $asignacion = $this->asignacion_m->get_asignacion($id_asignacion) OR show_404();
            
            if($asignacion->group_by)
            {
                $result_group = $this->db->group_by($asignacion->group_by)
                                    ->get($asignacion->table)
                                    ->result();
                foreach($result_group as $group)
                {
                    
                    //if(!in_array($group->{$asignacion->group_by},$groups))
                   // {
                        $groups[] = array('name'=>$group->{$asignacion->group_by},'checked'=>true);
                    //}
                }
            }
        }
        
        
        $this->template->title($this->module_details['name'])
            ->append_js('module::encuesta.controller.js')
            ->append_metadata('<script type="text/javascript">var id_cuestionario='.$id_cuestionario.',id_asignacion='.$id_asignacion.', groups='.json_encode($groups).';</script>')
            ->set('asignacion',$asignacion)
            ->set('groups',$groups)
            ->build('admin/report');
    }
    
    function chart($download=false)
    {
        
        set_time_limit(0);
        $result = array(
            'status'  => 0,
            'data'    => array(
                'total'      => 0,
                'inactivos'  => 0,
                'preguntas'  => array()
                
            ),
            'message' => ''
        );
        $asignacion      = false;
        $id_cuestionario = $this->input->get('id_cuestionario');
        $id_asignacion   = $this->input->get('id_asignacion');
        $groups          = $this->input->get('groups');
        
        
        
        $base_where = array(
            'id_cuestionario' => $id_cuestionario
        
        );
        
        if($id_asignacion && $id_asignacion != 0 )
        {
            $base_where['id_asignacion'] = $id_asignacion;
            
            $asignacion = $this->asignacion_m->get_asignacion($id_asignacion);
        }
        
        if($asignacion)
        {
            if($asignacion->group_by)
            {
                $table_ids = array();
                
                $result_table = $this->db->where_in($asignacion->group_by,$groups)->get($asignacion->table)->result();
                
                if($result_table)
                {
                    
                     $table_ids = array_for_select($result_table,$asignacion->table_id,$asignacion->table_id);
                     
                     $base_where[' table_id IN ("'.implode('","',$table_ids).'")'] = null;
                }
                
               
                
                
            }
            
        }
       
        $fields = $this->encuesta->build_form($id_cuestionario); 
        
        $all = $this->encuesta_m->where($base_where)
                        
                    
                    ->get_all();
                    
        if(!$all || !$groups)
        {
            $result['message'] = lang('global:not_found');
            echo json_encode($result);
            exit();
        }
        
        
       $result['status'] = true;
        
        foreach($all as $item)
        {
            $result['data']['total'] ++;
            
            if($item->activo == 0)
            {
                $result['data']['inactivos'] ++;
            } 
        }
        
   
        
        
        //Extrae las preguntas por cuestionarioo o preguntas.
        foreach($fields as &$field)
        {
            
            if($field['tipo'] != 'text'){
                foreach($field['opciones'] as &$opcion)
                {
                    $opcion['cantidad'] = $this->db->where(array_merge($base_where,array(
                                'id_pregunta'=> $field['id'],
                                'respuesta'  => $opcion['label']
                                )))
                                ->join('encuestas','encuestas.id=encuesta_respuestas.id_encuesta')
                                ->count_all_results('encuesta_respuestas');
                }
            }
            else
            {
                $options = $this->db->select('count(*) AS cantidad,respuesta AS label')->where($base_where)
                                    
                                    ->where('id_pregunta',$field['id'])->group_by('respuesta')
                                    ->join('encuestas','encuestas.id=encuesta_respuestas.id_encuesta')
                                    ->get('encuesta_respuestas')->result();
                $field['opciones'] = $options;
            }
        }
        $result['data']['preguntas'] = $fields;
       
        if($download)
        {
            $this->load->library('Excel');
            
            error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);
        
            define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
        
            date_default_timezone_set('Europe/London');
            $columns = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
            
            $this->excel->getActiveSheet()->getHeaderFooter()->setOddHeader(implode(',',$groups));
            $active_sheet = $this->excel->getActiveSheet();
            
            
            $rows_array   = array();
            
            
            $inc_field = 1;
            
            //print_r($fields);
            /*foreach($result['data']['preguntas'] as $pregunta)
            {
                echo $pregunta['titulo'].'<br/>';
            }*/
            foreach($fields as $pregunta)
            {
                
               
                
                $rows_array[] = array($inc_field.' .- '.strtoupper(replace_string($pregunta['titulo'])),'');
                
                $rows_array[] = array('DESCRIPCION','VALOR');
                $row_active = (count($rows_array));
                
                $active_sheet->getRowDimension($row_active-1)->setRowHeight(26);
                $active_sheet->getRowDimension($row_active)->setRowHeight(20);
                
                $active_sheet->getStyle('A')->getAlignment()->setWrapText(true);
                $active_sheet->getColumnDimension('A')->setWidth(32);
                $dataseriesLabels = array(
	                  new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$'.$row_active, NULL, 1),
                );
                $total = 0;
                foreach($pregunta['opciones'] as $opcion)
                {
                     $rows_array[] = array($opcion['label'],$opcion['cantidad']);
                     
                     $total += $opcion['cantidad'];
                }
                
                $xAxisTickValues = array(
                	new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$'.($row_active+1).':$A$'.count($rows_array), NULL, 4),//Respuesta1 a RespuestaN
                   
                );
                $dataSeriesValues = array(
                	new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$'.($row_active+1).':$B$'.count($rows_array), NULL, 4),
                
                );
                //	Build the dataseries
                $series = new PHPExcel_Chart_DataSeries(
                	PHPExcel_Chart_DataSeries::TYPE_BARCHART,		// plotType
                	PHPExcel_Chart_DataSeries::GROUPING_STANDARD,	// plotGrouping
                	range(0, count($dataSeriesValues)-1),			// plotOrder
                	$dataseriesLabels,								// plotLabel
                	$xAxisTickValues,								// plotCategory
                	$dataSeriesValues								// plotValues
                );
                //	Set additional dataseries parameters
                //		Make it a vertical column rather than a horizontal bar graph
                $series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
                
                //	Set the series in the plot area
                $plotarea = new PHPExcel_Chart_PlotArea(NULL, array($series));
                //	Set the chart legend
                $legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
                
                $title = new PHPExcel_Chart_Title($pregunta['titulo']);
                $yAxisLabel = new PHPExcel_Chart_Title('Respuestas');
                $rows_array[] = array('TOTAL',$total);
                
                //	Create the chart
                $chart = new PHPExcel_Chart(
                	'chart1',		// name
                	$title,			// title
                	$legend,		// legend
                	$plotarea,		// plotArea
                	true,			// plotVisibleOnly
                	0,				// displayBlanksAs
                	NULL,			// xAxisLabel
                	$yAxisLabel		// yAxisLabel
                );
                
                //	Set the position where the chart should appear in the worksheet
                $chart->setTopLeftPosition('C'.($row_active-1));
                $chart->setBottomRightPosition('L'.($row_active+count($pregunta['opciones'])+4));
                
                //	Add the chart to the worksheet
                $active_sheet->addChart($chart);
                
                
                
                 $rows_array[] = array('','');
                 $rows_array[] = array('','');
                 $rows_array[] = array('','');
                 //Inser chart
                 
                 
                 $inc_field++;
            }
            
            $active_sheet->getStyle('A1:A'.count($rows_array))->getFont()->setSize(10);
            $active_sheet->getStyle('B1:B'.count($rows_array))->getFont()->setSize(10);
            
            //$active_sheet->getRowDimension('A1:A100')->setRowHeight(20);
            $active_sheet->getStyle('A')->getAlignment()->setWrapText(true);// setRowHeight(40);
            $active_sheet->fromArray($rows_array);
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            //header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="encuesta_'.$id_cuestionario.'_'.$id_asignacion.'_'.now().'.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');
            
            // If you're serving to IE over SSL, then the following may be needed
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0
            //exit();
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
            $objWriter->setIncludeCharts(TRUE);
            $objWriter->save('php://output');
        }
        else{
            echo  json_encode($result);
            exit();
        }
        
    }
    function export($id_cuestionario,$id_asignacion='')
    {
        ini_set('memory_limit','600M');
   	    $this->load->helper('download');
		$this->load->library('format');
        
        $group      = $this->input->get('group');
        $activo     = $this->input->get('activo');
        $file_name  = 'encuesta_'.$id_cuestionario;
        $table_ids  = array();
        $base_where = array(
        
            'id_cuestionario' => $id_cuestionario,
            
        );
        
        if($id_asignacion)
        {
            $base_where['id_asignacion']   = $id_asignacion;
        }
        if(is_numeric($activo))
        {
            $base_where['encuestas.activo']   = $activo;
        }
        
        
        
        $asignacion = false;
           
        if($id_asignacion)
        {  
            $asignacion = $this->asignacion_m->get($id_asignacion) OR redirect('admin/encuestas');
            
            $file_name.='_'.$id_asignacion;
            
            if($group)
            {
                $table_ids = $this->db->where($asignacion->group_by,$group)
                            ->get($asignacion->table)->result();
                            
                $this->db->where_in('table_id',array_for_select($table_ids,$asignacion->table_id,$asignacion->table_id));
            }              
            /*
            $base_where['id_asignacion'] = $id_asignacion;
            $asignacion = $this->asignacion_m->get($id_asignacion);
            
            
            if($asignacion->group_by){
                
                $groups = $this->db->order_by($asignacion->group_by)
                                ->get($asignacion->table)
                                ->result();
                
                if($group)
                {
                    $result_group = $this->db->where($asignacion->group_by,$group)
                                            ->get($asignacion->table)->result();
                    
                    $tables_ids = array_for_select($result_group,$asignacion->table_id,$asignacion->table_id);
                    
                    
                     $this->encuesta_m->where_in('table_id',$tables_ids);
                     
                     $encuestas = $this->encuesta_m->where($base_where)
                                    ->get_all();
                                    
                    
                }
                if($groups)
                {
                    $groups = array_for_select($groups,$asignacion->group_by,$asignacion->group_by);
                    
                    
                }
                
            }
            
            */
            
            
                
        }
        $this->db->join('encuesta_respuestas','encuesta_respuestas.id_encuesta=encuestas.id')
                ->order_by('id_encuesta');
        
        /*if($table_ids)
        {
            $this->db->where_in('table_id',array_for_select($table_ids,$asignacion->table_id,$asignacion->table_id));
        }*/
        
        
        $array = $this->db->where($base_where)
                    ->get('encuestas')->result_array();
        
        
        force_download($file_name.'.csv',$this->format->factory($array)->to_csv());
        exit();
    }
    
    function import()
    {
         ini_set('max_execution_time', 0); 
        $this->load->model(array(
            'files/file_folders_m'
        ));
        $this->load->library('files/files');
        
        
        //print_r($_FILES);
        $folder        = $this->file_folders_m->get_by_path('otros');
        $file_result   = Files::upload($folder->id,false,'file',false,false,false,'csv');
        $csv_array     = array();
        $updates = 0;
        $creates = 0;
        $result       = array(
            'status'  => false,
            'message' => '',
            'data'    => array('updates'=>0,'adds'=>0,'respuestas'=>0,'encuestas'=>0)
        
        );
        
        if($file_result['status'])
        {
           
           
           
            $file_path = $this->_path.'/'.$file_result['data']['filename'];
            
           
              
            $file   = fopen($file_path, 'r');
            $max_line_length = defined('MAX_LINE_LENGTH') ? MAX_LINE_LENGTH : 10000;
            $header          = fgetcsv($file,$max_line_length); 
            
            
           
            while (($line = fgetcsv($file)) !== FALSE) {
             
              $csv_array[] =  array_combine($header,$line);
            }
            fclose($file);
            
            //Borramos archivo
            
            Files::delete_file($file_result['data']['id']);
            
            $encuesta_tmp  = false;
            $data_encuesta = array(); 
            $table_id_tmp  = 0;
           
            
            $data_inserts = array();
            $inc_insert = 0;
            foreach($csv_array as $reg)
            {
                
                //if($inc_insert>100)
                  //  break;
                
                //unset($reg['id']);
                
                if(!isset($data_inserts[$reg['table_id']]))
                {
                    $data_inserts[$reg['table_id']] = array_merge(array(
                        'respuestas'=>array()
                    ),$reg);
                    
                    unset($data_inserts[$reg['table_id']]['respuesta'],$data_inserts[$reg['table_id']]['id'],$data_inserts[$reg['table_id']]['id_pregunta'],$data_inserts[$reg['table_id']]['id_encuesta'],$data_inserts[$reg['table_id']]['tipo_esc']);
                }
                
                $data_inserts[$reg['table_id']]['respuestas'][$reg['id_pregunta']][] =  $reg['respuesta'];
                
                
                
            }
            
            ///Aca vamos bien
            foreach($data_inserts as $insert)
            {
                
                $where_encuesta = array(
                    'id_asignacion'   => $insert['id_asignacion'],
                    'id_cuestionario' => $insert['id_cuestionario'],
                    'table_id'        => $insert['table_id'],
                
                );
                
                
                $encuesta   = $this->encuesta_m->get_by($where_encuesta);
                $respuestas = $insert['respuestas'];
                
                unset($insert['respuestas']);
                $insert['edad']    = $insert['edad']?(int)$insert['edad']:null;
                $insert['estatus'] = $insert['estatus']?(int)$insert['estatus']:null;
                $insert['created_on']=now();
                $insert['updated_on']=now(); 
                if($encuesta)
                {
                    
                    
                    if($this->encuesta_m->update($encuesta->id,$insert)){
                        $id_encuesta = $encuesta->id; 
                        $updates++;
                    }
                }
                else
                {
                   
                    if($id_encuesta = $this->encuesta_m->insert($insert))
                    {
                         $creates++;
                    }
                }
                
                //Agregamos respuestas
                
                foreach($respuestas as $id_pregunta => $data_respuestas)
                {
                    $ids_respuestas = array();
                    
                    foreach($data_respuestas as $respuesta)
                    {
                        $data_respuesta = array(
                            'id_pregunta' => $id_pregunta,
                            'respuesta'   => $respuesta,
                            'id_encuesta' => $id_encuesta
                        );
                        //print_r($data_respuesta);
                        if($result_respuesta = $this->db->where($data_respuesta)->get('encuesta_respuestas')->row())
                        {
                            $ids_respuestas[] = $result_respuesta->id;
                            
                            
                            
                            $this->db->where('id',$result_respuesta->id)
                                        ->set($data_respuesta)->update('encuesta_respuestas');
                        }
                        else
                        {
                           
                            
                            $this->db->set($data_respuesta)->insert('encuesta_respuestas');
                            $ids_respuestas[] = $this->db->insert_id();
                        }
                        
                    }
                    //Eliminamos respuestas no encontrados
                    
                    $this->db->where(array(
                            'id_encuesta' => $id_encuesta,
                            'id_pregunta' => $id_pregunta,
                        ))
                        ->where_not_in('id',$ids_respuestas)
                        ->delete('encuesta_respuestas');
                }
                $result['status']  = true; 
                $result['message'] = sprintf(lang('encuesta:import_success'),count($data_inserts),$creates,$updates);
                
            }
            
            //$result['data'] = $data_inserts;
            echo  json_encode($result);
        
            exit();
               
        }
        else
        {
            echo  json_encode($file_result);
        
            exit();
        }
        //Files::delete_file($file_result['data']['id']);
        
        
        
    }
    private function _update_encuesta($table_id,$data)
    {
        
    }
    public function inverter($id_cuestionario,$id_asignacion='',$field='')
    {
        $ejecutar = $this->input->get('ejecutar');
        $group    = $this->input->get('group');
        $updates  = array();
        $base_where = array(
            'id_cuestionario' => $id_cuestionario,
            //'tipo_esc'        => 'emsad'
        );
        
        
        if($id_asignacion)
        {
            $base_where['id_asignacion'] = $id_asignacion;
            $asignacion = $this->asignacion_m->get($id_asignacion);
            
            
            $table_ids = $this->db->where($asignacion->group_by,$group)
                            ->get($asignacion->table)->result();
                            
            $updates = array_for_select($table_ids,$field,$asignacion->table_id); 
                          
            $this->encuesta_m->where_in('table_id',array_keys($updates));
            
            
        }
        
        $encuestas = $this->encuesta_m->where($base_where)
                        ->get_all();
                        
             $inc = 1;
             $activos = 0;
            foreach($encuestas as $encuesta)
            {
                if($encuesta->activo) $activos++;
                if(isset($updates[$encuesta->table_id]))
                {
                    
                    
                        $data_update = array(
                            'table_id'=> $updates[$encuesta->table_id]
                        );
                      $ejecutar AND  $this->encuesta_m->update($encuesta->id,$data_update);
                      
                      echo '<p style="'.($encuesta->activo==1?'':'color:#f00;').'">'.$inc.' .- Encuesta actualizada de '.$encuesta->table_id.' a '.$updates[$encuesta->table_id].'</p>';
                    
                }
                else
                {
                    echo $inc.'.- Encuesta no encontrada '.$encuesta->table_id.'<br/>';
                }
                
                $inc++;
                
            }
        
                        
        echo count($encuestas).'  encuestas encontradas; '.$activos.' activos';
                        
        
    }
 }
 ?>