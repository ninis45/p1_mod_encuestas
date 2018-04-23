<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

//$route['medallero/admin/disciplinas/(:num)']			= 'admin_disciplinas/load/$1';
//$route['medallero/admin/(:num)']			= 'admin/load/$1';

$route['encuestas/admin/asignacion(/:any)?']			= 'admin_asignacion$1';

$route['encuestas/admin(:any)?']	= 'admin$1';
$route['encuestas(:any)?']			= 'encuestas_front$1';
?>