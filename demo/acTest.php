<?php
class acTest extends AbstractModule{

	public function __construct(){
		parent::__construct();
		$this->connect("localhost","root","root","information_schema"); // Conexión MySQL
	}

	public function methodTest(){
		echo "Hello Acore!!";

		$data = $this->model->querySelect("ENGINES");
		A::log($data);

		$this->view->paragraph = "[:XA]<p>[:ENGINE] [:SUPPORT] [:COMMENT]</p>";
		$this->view->paragraph($data);
	}

}