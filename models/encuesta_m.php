<?php defined('BASEPATH') or exit('No direct script access allowed');

class Encuesta_m extends MY_Model {

	private $folder;

	public function __construct()
	{
		parent::__construct();
		$this->_table = 'encuestas';
		
	}
    function get_by_table($id_asignacion=0,$table_id='')
    {
        return $this->get_by(array(
                'id_asignacion' => $id_asignacion,
                'table_id' => $table_id
            ));
    }
    function get_result($id)
    {
        $encuesta = $this->get($id);
        
        if($encuesta)
        {
            $values = $this->db->where('id_encuesta',$id)
                            ->get('encuesta_respuestas')
                            ->result();
            $encuesta->pregunta = array();
            if($values)
            {
                foreach($values as $value)
                {          
                    if(!isset($encuesta->pregunta[$value->id_pregunta]))
                    {
                        $encuesta->pregunta[$value->id_pregunta] = array();
                    }
                    $encuesta->pregunta[$value->id_pregunta][] = $value->respuesta;
                    //$encuesta->values = array_for_select($values,'id_pregunta','respuesta');
                }
            }
            
            
            
            return $encuesta;
        }
        return false;
    }
    
 }
?>