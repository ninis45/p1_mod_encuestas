<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin_Asignacion extends Admin_Controller {
	protected $section='asignacion';
	public function __construct()
	{
		parent::__construct();
        $this->lang->load('encuesta');
        $this->load->model(array(
            'cuestionarios/cuestionario_m',
            'asignacion_m'
        ));
        
        $this->validation_rules = array(
				
					array(
						'field' => 'titulo',
						'label' => 'Titulo',
						'rules' => 'trim|required'
						),
                    array(
						'field' => 'table',
						'label' => 'Tabla o módulo',
						'rules' => 'trim'
						),
                    array(
						'field' => 'table_id',
						'label' => 'Columna primaria',
						'rules' => 'trim'
						),
                     
                    array(
						'field' => 'slug',
						'label' => 'Slug',
						'rules' => 'trim|required'
						),
                    array(
						'field' => 'instrucciones',
						'label' => 'Instrucciones',
						'rules' => 'trim'
						),
                    array(
						'field' => 'tiempo',
						'label' => 'Tiempo',
						'rules' => 'trim|numeric'
						),
                     array(
						'field' => 'group_by',
						'label' => 'Agrupado',
						'rules' => 'trim'
						),
                     array(
						'field' => 'auth_by',
						'label' => 'Auntenticado',
						'rules' => 'trim'
						),
                     array(
						'field' => 'placeholder',
						'label' => 'Ayuda/Placeholder',
						'rules' => 'trim'
						),
                     array(
						'field' => 'aviso',
						'label' => 'Aviso de privacidad',
						'rules' => 'trim'
						),
                    
        );
    }
    function index()
    {
        
        $asignaciones = $this->asignacion_m->get_all();
        
        $this->template->title($this->module_details['name'])
			//->set('items',$this->cuestionario_m->get_all())
			//->set('pagination',false)
            //->set('cuestionarios',$cuestionarios)
            ->set('asignaciones',$asignaciones)
			->build('admin/asignacion/index');
    }
    function edit($id=0)
    {
        $asignacion = $this->asignacion_m->get_asignacion($id) OR show_404();
        
        //$asignacion->campos = $asignacion->campos?json_decode($asignacion->campos):array();
        $cuestionario = $this->cuestionario_m->get_cuestionario($asignacion->id_cuestionario) or show_error('El cuestionario no cargo correctamente');
        
        //$asignacion->campos = array_merge($asignacion->campos,$asignacion->cuestionario->campos);
        
        $this->form_validation->set_rules($this->validation_rules);
		if($this->form_validation->run())
		{
		     unset($_POST['btnAction']);
             $data = array(
                
                'titulo'          => $this->input->post('titulo'),
                'table'           => $this->input->post('table'),
                'table_id'        => $this->input->post('table_id'),
                'slug'            => $this->input->post('slug'),
                'group_by'        => $this->input->post('group_by'),
                'placeholder'     => $this->input->post('placeholder'),
                'aviso'           => $this->input->post('aviso'),
                'auth_by'        => $this->input->post('auth_by'),
                
                'comodin'           => $this->input->post('comodin'),
                'comodin_valor'     => $this->input->post('comodin_valor'),
                
                'tiempo'          => $this->input->post('tiempo')?$this->input->post('tiempo'):null,
                'instrucciones'   => $this->input->post('instrucciones'),
                'created_on'      => now(),
                'campos'          => $this->input->post('campos')?json_encode($this->input->post('campos')):null,
                
                'table_fields'    => $this->input->post('table_fields')?json_encode($this->input->post('table_fields')):null,
                
             );
			if($this->asignacion_m->update($id,$data))
			{
			 
				$this->session->set_flashdata('success',lang('asignacion:save_success'));
				redirect('admin/encuestas/asignacion/edit/'.$id);
			}
			else
			{
				$this->session->set_flashdata('success',lang('asignacion:save_error'));
				redirect('admin/encuestas/asignacion');
			}
          
        }
        
        if($_POST)
        {
            $asignacion = (Object)$_POST;
            
            if(isset($asignacion->campos)==false)
            {
                $asignacion->campos = array();
            }
            
            if(isset($asignacion->table_fields)==false)
            {
                $asignacion->table_fields = array();
            }
        }
        $modules = array(
        
            array(
                'slug' => 'alumnos',
                'name' => 'Alumnos',
                'rows' => array()
            ),
            array(
                'slug' => 'empleados',
                'name' => 'Empleados',
                'rows' => array()
            )
        );
        
        foreach($modules as &$module)
        {
            $module['rows'] = (array)$this->db->list_fields($module['slug']);
            //print_r($module);
            foreach($module['rows'] as &$row)
            {
                //$row['visible'] = true;
            }
            
        }
        
        //print_r($asignacion);
        $this->template->title($this->module_details['name'])
                        ->set('asignacion',$asignacion)
                        ->set('cuestionario',$cuestionario)
                        ->set('campos',array_merge(is_array($asignacion->campos)?$asignacion->campos:array(),$cuestionario->campos))
                        ->append_metadata('<script type="text/javascript">var modules ='.json_encode($modules).',rows_right='.json_encode($asignacion->table_fields?$asignacion->table_fields:array()).' ; </script>')
                        ->append_js('module::encuesta.controller.js')
                        ->build('admin/asignacion/form');
    }
    function create($id_cuestionario = 0 )
    {
        $asignacion = new StdClass();
        
        if($this->input->is_ajax_request()== false)
            $cuestionario = $this->cuestionario_m->get_cuestionario($id_cuestionario) or show_error('El cuestionario no cargo correctamente');
        
        $this->form_validation->set_rules($this->validation_rules);
		if($this->form_validation->run())
		{
		     unset($_POST['btnAction']);
             $data = array(
                'id_cuestionario' => $id_cuestionario,
                'titulo'          => $this->input->post('titulo'),
                'table'           => $this->input->post('table'),
                'table_id'           => $this->input->post('table_id'),
                'slug'            => $this->input->post('slug'),
                'tiempo'          => $this->input->post('tiempo')?$this->input->post('tiempo'):null,
                'group_by'        => $this->input->post('group_by'),
                'auth_by'        => $this->input->post('auth_by'),
                'aviso'           => $this->input->post('aviso'),
                'instrucciones'   => $this->input->post('instrucciones'),
                'created_on'      => now(),
                
                'comodin'           => $this->input->post('comodin'),
                'comodin_valor'     => $this->input->post('comodin_valor'),
                
                'campos'          => $this->input->post('campos')?json_encode($this->input->post('campos')):null,
                'table_fields'    => $this->input->post('table_fields')?json_encode($this->input->post('table_fields')):null,
                
             );
			if($id=$this->asignacion_m->insert($data))
			{
			 
				$this->session->set_flashdata('success','El cuestionario ha sido agregado satisfactoriamente');
				redirect('admin/encuestas/asignacion/edit/'.$id);
			}
			else
			{
				$this->session->set_flashdata('error','Error al tratar de guardar los datos del  cuestionario');
				redirect('admin/encuestas/asignacion/create');
			}
          
        }
        ///Asignamos los arreglos para que no nos marque error
        
        $this->validation_rules[] = array(
            'field' => 'campos'
        
        );
        
        $this->validation_rules[] = array(
            'field' => 'table_fields'
        
        );
        foreach ($this->validation_rules as $rule)
		{
			$asignacion->{$rule['field']} = $this->input->post($rule['field']);
		}
        
        $cuestionarios = $this->cuestionario_m->get_all();
        
        
        
        
        $modules = array(
        
            array(
                'slug' => 'alumnos',
                'name' => 'Alumnos',
                'rows' => array()
            ),
            array(
                'slug' => 'empleados',
                'name' => 'Empleados',
                'rows' => array()
            )
        );
        
        foreach($modules as &$module)
        {
            $module['rows'] = (array)$this->db->list_fields($module['slug']);
            //print_r($module);
            foreach($module['rows'] as &$row)
            {
                ///$row->table = $module['slug'];
                //$row['visible'] = true;
            }
            
        }
       
         $this->input->is_ajax_request()
         ? $this->template->set_layout(false)
                        ->set('cuestionarios',$cuestionarios)
                        ->build('admin/asignacion/list_cuestionario')
         :$this->template->title($this->module_details['name'])
                        ->set('asignacion',$asignacion)
                        ->set('cuestionario',$cuestionario)
                        ->set('campos',array_merge(is_array($asignacion->campos)?$asignacion->campos:array(),$cuestionario->campos))
                      
                        ->append_metadata('<script type="text/javascript">var  modules ='.json_encode($modules).',rows_right='.json_encode($asignacion->table_fields?$asignacion->table_fields:array()).' ; </script>')
                        ->append_js('module::encuesta.controller.js')
                        ->build('admin/asignacion/form');
    }
    
    function truncate($id=0)
    {
         role_or_die($this->section, 'truncate');
         $this->load->model('encuesta_m');
        
		$ids = array();//($id) ?array(0=>$id) : $this->input->post('action_to');
		$truncates = $this->encuesta_m->where('id_asignacion',$id)->get_all();
        
        if ( ! empty($truncates))
		{
		  
   	        foreach ($truncates as $encuesta)
			{
                
                $this->db->where('id_encuesta',$encuesta->id)->delete('encuesta_respuestas');
                $ids[] = $encuesta->id;
                /*if($encuesta = $this->registro_m->get($id))
                {
                    $this->registro_m->delete($id);
                    
                    $deletes[] = $registro->participante;
                }*/
            }
            $this->db->where_in('id',$ids)->delete('encuestas');
            
            $this->session->set_flashdata('success',lang('global:delete_success'));
        }
        
        redirect('admin/encuestas/asignacion');
    }
 }
 ?>