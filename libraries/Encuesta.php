<?php defined('BASEPATH') or exit('No direct script access allowed');
class Encuesta
{
	public $values = array();
	// --------------------------------------------------------------------------
    public function __construct()
    {
        ci()->load->helper('form');
        ci()->load->model(array(
            'cuestionarios/pregunta_m',
            'encuestas/encuesta_m',
            'encuestas/opciones_m'
        ));
    }
    public function set_values($values = array())
    {
        $this->values = $values;
        return $this;
    }
    
    public static function SaveResource($configuracion,$id,$input)
    {
        if(empty($configuracion->table_fields)== false)
        {
            $data_update = array(
            
            );
            foreach($configuracion->table_fields as $field)
            {
                
                    $data_update[$field->slug] = $input[$field->slug];
            }
            
            if($data_update)
            {
                return ci()->db->where($configuracion->table_id,$id)
                        ->set($data_update)
                        ->update($configuracion->table);
            }
            return false;
        }
       
    }
   
    ///Genera las encuestas derivado de las asignaciones
    public static function Generate($asignacion,$group='')
    {
        ci()->load->library('encrypt');
        $base_where = array();
        
        if($asignacion->group_by && $group != '')
        {
            $base_where[$asignacion->group_by] = $group;
        }
        //ci()->load->model($module.'_m');
        ///print_r($base_where);
        $result  = ci()->db->where($base_where)->get($asignacion->table)->result();
        $updates = array();
        if($result)
        {
            foreach($result as $row)
            {
                //Verificamos que no se agregue otra vez el registro
                if(ci()->encuesta_m->get_by_table($asignacion->id,$row->{$asignacion->table_id}))
                {
                    //print_r($row);
                    //echo 'Esta existe '.$row->{$asignacion->table_id}.'<br/>';
                    continue;
                }
                $data = array(
                    'id_asignacion'   => $asignacion->id,
                    'id_cuestionario' => $asignacion->id_cuestionario,
                    'activo'          => '1',
                    'table'           => $asignacion->table,
                    'table_id'        => $row->{$asignacion->table_id},
                    'created_on'      => now(),
                    'codigo'          => base64_encode($asignacion->id.'_'.$row->{$asignacion->table_id})///ci()->encrypt->encode($capitulo->id,'abc')
                    
                );
                $id = ci()->encuesta_m->insert($data);
                
                $updates[$id] = $data;
            }
            //Se suprimio talve al momento de importar tenga problemas
            /*foreach($updates as $id=>$update)
            {
                ci()->encuesta_m->update($id,array(
                
                    'codigo' =>  base64_encode($id)//ci()->encrypt->encode($id)
                ));
            }*/
            
        }
    }
    public static function SaveValues($id_encuesta,$preguntas)
    {
        //Eliminamos las respuestas
        ci()->db->where('id_encuesta',$id_encuesta)
                ->delete('encuesta_respuestas');
                
        if(empty($preguntas) == false)
        {
            foreach($preguntas as $id_pregunta=>$pregunta)
            {
                foreach($pregunta as $value)
                {
                    $values_insert = array(
                        'id_pregunta' => $id_pregunta,
                        'id_encuesta' => $id_encuesta,
                        'respuesta'   => $value
                    );
                    
                    ci()->db->set($values_insert)
                                ->insert('encuesta_respuestas');
                                
                }
            }
        }
    }
    
    /******Genera el cuestionario de preguntas dinamicas******/
    public  function build_form($id_cuestionario,$tipo=1,$return_html=false)
    {
       
        //if(empty($tipo)==true && $tipo != 1)
        //{
            $data = array();
        
            $preguntas = ci()->pregunta_m->select('*,cat_preguntas.id AS id_pregunta')
                        ->where('cat_preguntas.id_cuestionario',$id_cuestionario)
                        //->where('cat_preguntas.titulo <> \'\'',NULL)
                        ->join('cat_pregunta_opciones','cat_pregunta_opciones.id_pregunta = cat_preguntas.id','LEFT')                
                        ->order_by('ordering')
                        ->get_all();                
            
            $id_pregunta = 0; 
            $tipo_pregunta = '';
            $opciones    = array();           
            foreach($preguntas as $pregunta)
            {
                //if(!$pregunta->respuesta)continue;
                
                if($tipo==1 && $id_pregunta==0){
                    $id_pregunta = $pregunta->id_pregunta;
                    
                    $tipo_pregunta = $pregunta->tipo;
                }
                    
                if($id_pregunta==$pregunta->id_pregunta)
                {
                    
                    $opciones[] = $pregunta->respuesta;
                }
                if(!isset($data[$pregunta->id_pregunta]))
                {
                    $data[$pregunta->id_pregunta] = array(
                        'id'       => $pregunta->id_pregunta,
                        'titulo'   => $pregunta->titulo,
                        'opciones' => array(),
                        'tipo'     => $pregunta->tipo,
                        'obligatorio' => $pregunta->obligatorio,
                        'muestra' => $pregunta->muestra,
                        'orden'   => $pregunta->ordering,
                        'html'    => '',
                        'rules'   => 'trim'.($pregunta->obligatorio?'|required':'')
                        
                    );
                  }  
                    switch($pregunta->tipo)
                    {
                        case 'text' :
                            $html = form_input('pregunta['.$pregunta->id_pregunta.'][]',(empty($this->values[$pregunta->id_pregunta])==false?$this->values[$pregunta->id_pregunta][0]:null),'class="form-control"');
                        break;
                        case 'radio':
                            $html = form_radio('pregunta['.$pregunta->id_pregunta.'][]',$pregunta->respuesta,empty($this->values[$pregunta->id_pregunta])==false && in_array($pregunta->respuesta,$this->values[$pregunta->id_pregunta]));
                        break;
                        case 'checkbox':
                            $html = form_checkbox('pregunta['.$pregunta->id_pregunta.'][]',$pregunta->respuesta,empty($this->values[$pregunta->id_pregunta])==false && in_array($pregunta->respuesta,$this->values[$pregunta->id_pregunta]));
                        break;
                    }
                    
                    if($pregunta->respuesta || $pregunta->tipo=='text'){
                        $data[$pregunta->id_pregunta]['opciones'][] = array(
                        
                            'input' =>   $html,
                            'label' =>   $pregunta->respuesta
                            
                        );
                    }
                    
                
            }
           
            
            //Asigna las repuestas a las demas preguntas de la primera pregunta
            if($id_pregunta)
            {
                foreach($data AS $id=>$preguntas)
                {
                    
                    //echo $id_pregunta;
                    if($id != $id_pregunta)
                    {
                        
                        foreach($opciones AS $opcion)
                        {
                            switch($tipo_pregunta)
                            {
                                case 'text' :
                                    $html = form_input('pregunta['.$id.'][]',(empty($this->values[$pregunta->id_pregunta])==false?$this->values[$id][0]:null),'class="form-control"');
                                break;
                                case 'radio':
                                    $html = form_radio('pregunta['.$id.'][]',$opcion,empty($this->values[$id])==false && in_array($opcion,$this->values[$id]));
                                break;
                                case 'checkbox':
                                    $html = form_checkbox('pregunta['.$id.'][]',$opcion,empty($this->values[$id])==false && in_array($opcion,$this->values[$id]));
                                break;
                            }
                           //echo 'Pregunta = '.$id.'<br/>';
                           $data[$id]['opciones'][] = array(
                                
                                    'input' =>   $html,
                                    'label' =>   $opcion
                                    
                           );
                            
                        }
                   }
                }
            }
             
       // return $data;            
           
        
        //}
        
            /*else
            {
                $data = array();
                $preguntas_tabla = ci()->pregunta_m->select('*,cat_preguntas.id AS id_pregunta')
                        ->where('cat_preguntas.id_cuestionario',$id_cuestionario)                
                        ->order_by('ordering')
                        ->get_all();
                    $base_where = array(         
                     'cat_preguntas.id_cuestionario' => $id_cuestionario,
                     'default_cat_preguntas.tipo' => 'table' 
                        );
                     
                    $respuestas = ci()->opciones_m->distinct()->select('respuesta ')
                            ->where($base_where)
                            ->join('cat_preguntas','cat_pregunta_opciones.id_cuestionario = cat_preguntas.id_cuestionario','LEFT')
                            ->get_all();
            
                foreach($preguntas_tabla as $pregunta)
                {
               
                            if(!isset($data[$pregunta->id_pregunta]))
                                 {
                                     $data[$pregunta->id_pregunta] = array(
                                    'id'       => $pregunta->id_pregunta,
                                    'titulo'   => $pregunta->titulo,
                                    'opciones' => array(),
                                    'tipo'     => $pregunta->tipo,
                                    'obligatorio' => $pregunta->obligatorio,
                                    'muestra' => $pregunta->muestra,
                                    'orden'   => $pregunta->ordering,
                                    'html'    => '',
                                    'rules'   => 'trim'.($pregunta->obligatorio?'|required':'')
                                      );
                                }  
                            foreach($respuestas as $respuesta)
                            {
                               
                            $html = form_radio('pregunta['.$pregunta->id_pregunta.'][]',$respuesta->respuesta,empty($this->values[$pregunta->id_pregunta])==false && in_array($respuesta->respuesta,$this->values[$pregunta->id_pregunta]));
                       
                             $data[$pregunta->id_pregunta]['opciones'][] = array
                            (
                                'input' =>   $html
                            );
                             
                            }  
                }
               
           
        }*/
            //print_r($data);
            return $data;
        }
      
    
    
}
?>