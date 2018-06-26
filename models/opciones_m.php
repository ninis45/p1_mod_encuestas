<?php defined('BASEPATH') or exit('No direct script access allowed');
class Opciones_m extends MY_Model {
	private $folder;
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'cat_pregunta_opciones';
		
	}
 }
 ?>