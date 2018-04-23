<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Encuestas_front extends Public_Controller
{
    public function __construct()
    {
          parent::__construct();
          $this->lang->load('encuesta');
          $this->load->model(array(
            'cuestionarios/cuestionario_m',
            'encuestas/asignacion_m',
            'encuestas/encuesta_m'
          ));
          
          $this->load->library('encuesta');
          $this->validation_rules = array(
				     'telefono'=>array(
						'field' => 'telefono',
						'label' => 'Telefono',
						'rules' => 'trim'
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
						'rules' => 'trim|valid_email|required'
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
                    'social_instagram'=>array(
						'field' => 'social_instagram',
						'label' => 'Instagram',
						'rules' => 'trim'
						),  
                    'social_whatsapp'=>array(
						'field' => 'social_whatsapp',
						'label' => 'Whatsapp',
						'rules' => 'trim'
						),  
                    'social_otro'=>array(
						'field' => 'social_otro',
						'label' => 'Otra red social',
						'rules' => 'trim'
						),                       
          );
          
          
          $this->template->set_breadcrumb('Encuestas','encuestas');
    }
    
    //Llenado a traves de asignacion
    public function llenado($id='')
    {
        
        
        $encuesta = false;
        $auth      = $this->input->get('auth');
        $aviso     = $this->input->get('aviso');
        $comodin   = $this->input->get('comodin');
        
        $validation_rules = array();
       
        if(is_string($id))
        {
            $asignacion = $this->asignacion_m->get_by_slug($id) or show_404();
            
            
        }
        
        
        $cuestionario = $this->cuestionario_m
                                    ->get($asignacion->id_cuestionario) OR show_error('No existe el cuestionario');
                                    
        if($cuestionario->publicar == 0)
        {
            show_404();
        }
         
        $cuestionario->campos = $cuestionario->campos?json_decode($cuestionario->campos):array();
        
        
       
        //Asignamos el required a los campos activos
        foreach(array_merge($cuestionario->campos,$asignacion->campos) as $campo)
        {
            //$this->validation_rules[$campo]['rules'] .= '|required';     
        }
        
        if($asignacion->aviso && $_GET && !$aviso)
        {
                $this->session->set_flashdata('error',lang('encuesta:error_aviso'));
                
                redirect('encuestas/llenado/'.$id);
        }
        
        
        if($auth)
        {
            
            $encuesta = false;
            
            
            if($asignacion->auth_by == 'codigo')
            {
                $encuesta = $this->encuesta_m->get_by('codigo',$auth);
            }
            else
            {
                $extra_table = array(
                    $asignacion->auth_by => $auth
                );
                
                if($asignacion->comodin)
                {
                    $extra_table[$asignacion->comodin] = $asignacion->comodin_valor;
                }
                
                
                $table = (array)$this->db//->where('escuela','LERMA')
                                        //->where($asignacion->auth_by,$auth)
                                        ->where($extra_table)
                                        ->get($asignacion->table)->row();
                 
                if($table)
                {
                     $base_where = array(
                        'id_asignacion'   => $asignacion->id,
                        'id_cuestionario' => $cuestionario->id,
                        'table_id'        => $table[$asignacion->table_id]
                     );
                     $encuesta = $this->encuesta_m->get_by($base_where) OR show_404();
                     
                     $encuesta->{$asignacion->table} = $table;
                }                       
                   
            }
           
            
            if(!$encuesta || $encuesta->activo == 0)
            {
                $this->session->set_flashdata('error',lang('encuesta:active_error'));
            
                redirect('encuestas/llenado/'.$id);
            }
            
            //Verificar si se puede optimizar esta funcion.
            //$encuesta->{$asignacion->table} = (array)$this->db->where($asignacion->table_id,$encuesta->table_id)
              //                          ->where('escuela','LERMA')
                //                        ->get($asignacion->table)->row();
                                        
            
        }
        
        
        
        //Extraemos las preguntas de la encuesta
        $fields = $this->encuesta->set_values($this->input->post('pregunta'))
                            ->build_form($asignacion->id_cuestionario);
                            
                         
        if($fields)
        {
            foreach($fields as $id_pregunta => $field)
            {
                $validation_rules[] = array(
                    'label' => $field['titulo'],
                    'field' => 'pregunta['.$id_pregunta.']',
                    'rules' => $field['rules']
                    
                );
            }
        }
        $this->validation_rules = array_merge($validation_rules,$this->validation_rules);
        
        $this->form_validation->set_rules($this->validation_rules);
        if($this->form_validation->run())
		 
        {
            $data  = array(
                //'tipo_esc'    => 'plantel',
                'updated_on' => now(),
                'ip'         => $this->input->ip_address(),
                'activo'     => 0
            );
           
            
            foreach(array_merge($cuestionario->campos,$asignacion->campos) as $campo)
            {
                $data[$campo] = $this->input->post($campo)?$this->input->post($campo):null;
            }
            if($this->encuesta_m->update($encuesta->id,$data))
            {
                Encuesta::SaveResource($asignacion,$encuesta->table_id,$this->input->post($asignacion->table));
                Encuesta::SaveValues($encuesta->id,$this->input->post('pregunta'));
                $this->session->set_flashdata('success',lang('encuesta:save_success'));
            }
            else
            {
                $this->session->set_flashdata('error',lang('encuesta:save_error'));
            }
            redirect('encuestas/llenado/'.$id);
            
        }
        
         if($_POST)
         {
            $encuesta = (Object)$_POST;
         }
        
         
         $this->template->title($this->module_details['name'])
                    ->set_layout('basic.html')
                    ->append_css('module::encuesta.css')
                    ->set('cuestionario',$cuestionario)
                    ->set('asignacion',$asignacion)
                    ->set('auth',$auth)
                    ->set('encuesta',$encuesta)
                   
                    ->set('campos_encuesta',array_merge($cuestionario->campos,$asignacion->campos))
                    ->set('fields',$fields)
                    ->build('form');
        
        
    }
    
    function aviso()
    {
        
    }
}
?>