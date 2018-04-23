<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Plugin_Encuestas extends Plugin
{
    public $version = '1.0.0';

	public $name = array(
		'en' => 'Search',
            'fa' => 'جستجو',
	);

	public $description = array(
		'en' => 'Create a search form and display search results.',
        'fa' => 'ایجاد فرم جستجو و نمایش نتایج',
	);
    public function __construct()
	{
    }
    public function display_form()
    {
        
    }
}
?>