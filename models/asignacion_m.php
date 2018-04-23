<?php defined('BASEPATH') or exit('No direct script access allowed');

class Asignacion_m extends MY_Model {

	private $folder;

	public function __construct()
	{
		parent::__construct();
		$this->_table = 'encuesta_asignaciones';
		
	}
    function get_by_slug($slug='')
    {
        $result = $this->get_by('slug',$slug);
        
        
        if($result)
        {
            $result->campos       = $result->campos?json_decode($result->campos):array();
            
            $result->table_fields = $result->table_fields?json_decode($result->table_fields):array();
            
            return $result;
        }
        
        return false;
    
        
    }
    function get_asignacion($id=0)
    {
        $result = $this->get($id);
        
        if($result)
        {
            $result->campos = $result->campos?json_decode($result->campos):array();
            
            $result->table_fields = $result->table_fields?json_decode($result->table_fields):array();
            
            return $result;
        }
        return false;
    }
 }
 ?>