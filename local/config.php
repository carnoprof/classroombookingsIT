<?php
defined('BASEPATH') OR exit('No direct script access allowed');

return array(

	'config' => array(
		'base_url' => 'https://ramonacongiu.altervista.org/labbook/',
		'log_threshold' => 1,
		'index_page' => 'index.php',
		'uri_protocol' => 'REQUEST_URI',
	),

	'database' => array(
		'hostname' => 'localhost',
		'port' => '3306',
		'username' => 'ramona',
		'password' => 'ramonacongiu',
		'database' => 'my_ramonacongiu',
		'dbdriver' => 'mysqli',
	),

);
