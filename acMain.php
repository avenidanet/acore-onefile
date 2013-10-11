<?php
/**
 * ACore OneFile v.1.0.0
 *
 * Simple framework php
 *
 * @author Brian Salazar [Avenidanet]
 * @link http://www.avenidanet.com
 * @copyright Brian Salazar 2006-2013
 * @license http://mit-license.org
 *
 * EXAMPLE MODULE main
 */

/*
 * Configuration
 */
$config = Settings::Init();
$config->host = 'localhost';
$config->user = 'root';
$config->pass = 'root';
$config->database = 'usuarios';

/*
 * Module main
 */
class acMain extends AbstractModule{
	
	public function __construct(){
		//Desactivar DB
		parent::__construct(TRUE);
	}
	
	//Method example
	public function inicio(){
		$data = $this->model->querySelect("usuarios");
		
		echo "<h2>Settings (vars comunes)</h2>";
		A::log($this->acore);
		
		echo "<h2>Metodos</h2>";
		A::log($data[0]);
		
		echo "<h2>Uso del view</h2>";
		$this->view->input = "[:id]<input type='text' />[:email] [:fecha] [:identificacion]<br/>";
		$this->view->input($data);
		
		$this->view->otro = "hola";
		$this->view->otro();
		
	}
	
}