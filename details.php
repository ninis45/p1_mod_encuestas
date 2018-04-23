<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Groups module
 *
 * @author PyroCMS Dev Team
 * @package PyroCMS\Core\Modules\Groups
 */
 class Module_Encuestas extends Module
{

	public $version = '1.0';

	public function info()
	{
		$info= array(
			'name' => array(
				'en' => 'NA',
				
				'es' => 'Encuestas',
				
			),
			'description' => array(
				'en' => 'You can upload your favorite videos from YOUTUBE',
				
				'es' => 'Realiza tests o encuestas en tu página web',
				
			),
			'frontend' => false,
			'backend' => true,
			'menu' => 'content',
            'roles' => array(
				'create', 'edit','delete'
			),
			'sections'=>array(
                'encuestas'=>array(
                    'name'=>'encuesta:title',                    
                    'uri' => 'admin/encuestas',
        			'shortcuts' => array(
        				array(
        					'name' => 'encuesta:add',
        					'uri' => 'admin/encuestas/create/{{id}}',
        					'class' => 'btn btn-success',
                            'ng-if' => 'show_add'
                           
        				),
                        
                       
                       
        			)
                ),
                /*'obligaciones'=>array(
                    'name'=>'obligaciones:title',                    
                    'uri' => 'admin/transparencia/obligaciones',
        			'shortcuts' => array(
        				array(
        					'name'       => 'obligaciones:create',
        					'uri'        => 'admin/transparencia/obligaciones/create',
        					'class'      => 'btn btn-success',
                            'open-modal' => '',
                            'modal-title' => 'Seleccionar fracción'
                           
        				),
                       
        			)
                ),
                'fracciones'=>array(
                    'name'=>'fracciones:title',                    
                    'uri' => 'admin/transparencia/fracciones',
        			'shortcuts' => array(
        				array(
        					'name' => 'fracciones:create',
        					'uri' => 'admin/transparencia/fracciones/create',
        					'class' => 'btn btn-success',
                           
        				),
                       
        			)
                ),*/
           )
		);
        
        if (function_exists('group_has_role'))
		{
		    if(group_has_role('encuestas', 'admin_asignacion'))
			{
			    
				$info['sections']['asignacion'] = array(
				    'name'=>'asignacion:title',                    
                    'uri' => 'admin/encuestas/asignacion',
        			'shortcuts' => array(
        				array(
        					'name'       => 'asignacion:create',
        					'uri'        => 'admin/encuestas/asignacion/create',
        					'class'      => 'btn btn-success',
                            'open-modal' => '',
                            'modal-title' => 'Seleccionar cuestionario'
                           
        				),
                       
        			)
				);
			}
			/*if(group_has_role('transparencia', 'admin_fracciones'))
			{
			    
				$info['sections']['fracciones'] = array(
				    'name'=>'fracciones:title',                    
                    'uri' => 'admin/transparencia/fracciones',
        			'shortcuts' => array(
        				array(
        					'name' => 'fracciones:create',
        					'uri' => 'admin/transparencia/fracciones/create',
        					'class' => 'btn btn-success',
                           
        				),
                        array(
        					'name' => 'fracciones:ordering',
        					'uri' => 'admin/transparencia/fracciones?order=1',
        					'class' => 'btn btn-primary',
                           
        				),
                       
        			)
				);
			}*/
            
		}
        
        return $info;
	}

	public function install()
	{
		$this->dbforge->drop_table('encuestas');
        $this->dbforge->drop_table('encuesta_respuestas');
        $this->dbforge->drop_table('encuesta_asignaciones');
		$tables = array(
			'encuestas' => array(
				'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true,),
				'id_cuestionario' => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                'id_asignacion'   => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                'codigo'       => array('type' => 'VARCHAR', 'constraint' => 254, 'null' => true),
                'email'       => array('type' => 'VARCHAR', 'constraint' => 254, 'null' => true),
                'ip'          => array('type' => 'VARCHAR', 'constraint' => 254, 'null' => true),
                'sexo'        => array('type' => 'VARCHAR', 'constraint' => 254, 'null' => true),
                'telefono'    => array('type' => 'VARCHAR', 'constraint' => 254, 'null' => true),
                'edad'        => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                //'edad'        => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                
                'social_facebook'    => array('type' => 'VARCHAR', 'constraint' => 254, 'null' => true),
                'social_twitter'     => array('type' => 'VARCHAR', 'constraint' => 254, 'null' => true),
                'social_otro'        => array('type' => 'VARCHAR', 'constraint' => 254, 'null' => true),
                
                'created_on'  => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                'updated_on'  => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                'estatus'     => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                
                'table'        => array('type' => 'VARCHAR', 'constraint' => 254, 'null' => true),
                'table_id'     => array('type' => 'VARCHAR', 'constraint' => 254, 'null' => true),
                
                'activo'     => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                
                
                
			),
          
            'encuesta_respuestas'  => array(
				'id'          => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true,),
   	            'id_encuesta' => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                'id_pregunta' => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                'respuesta'   => array('type' => 'VARCHAR', 'constraint' => 254,),
                
                
                'created_on'  => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                
               
				
				
			),
             'encuesta_asignaciones'  => array(
				'id'          => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true,),
   	            'id_cuestionario' => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                //'id_fraccion_obligacion' => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                'campos'       => array('type' => 'TEXT', 'null' => true),
                'instrucciones' => array('type' => 'TEXT', 'null' => true),
				
                'created_on'   => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                'updated_on'   => array('type' => 'INT', 'constraint' => 11, 'null' => true),
                
                'titulo'       => array('type' => 'VARCHAR', 'constraint' => 254,'null' => true),
                
                'table'        => array('type' => 'VARCHAR', 'constraint' => 254,'null' => true),
                'table_id'     => array('type' => 'VARCHAR', 'constraint' => 254,'null' => true),
                
                'group_by'     => array('type' => 'VARCHAR', 'constraint' => 254,'null' => true),
                'tiempo'       => array('type' => 'INT', 'constraint' => 11, 'null' => true),
				
				
			),
		);

		if ( ! $this->install_tables($tables))
		{
			return false;
		}

		

		return true;
	}

	public function uninstall()
	{
		// This is a core module, lets keep it around.
		return false;
	}

	public function upgrade($old_version)
	{
		return true;
	}

}
?>