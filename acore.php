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
*/

/*
 * CLASE A | ACORE
 */
class A{
	
	private function __construct(){
	}
	
	public static function script($data,$load=''){
		$CDN = array(	'jquery'=>'<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>',
						'angular'=>'<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.0.3/angular.min.js"></script>',
						'swfobject'=>'<script src="//ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>',
						'validate'=>'<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>',
						'gmaps'=>'<script src="//maps.googleapis.com/maps/api/js?v=3&sensor=false"></script>');
		$jss = explode(',',$data);
		foreach ($jss as $js){
			echo $CDN[$js];
		}
		if($load != ''){
			$jss = self::files($load);
			foreach ($jss as $js){
				if(substr($js,-3) == ".js"){
					echo '<script src="'.$js.'"></script>';
				}
			}
		}
	}
	
	public static function ng_params(){
		return json_decode(file_get_contents('php://input'));
	}
	
	public static function validate($string,$type='text'){
		$patterns = array(	'text'=>'/^[a-z\d_ .áéíóúñ]{1,255}$/i',
				'number'=>'/^[0-9]{1,20}$/i',
				'name'=>'/^[a-z\d_ .áéíóúñ]{4,60}$/i',
				'email'=>'/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',
				'id'=>'/^\d{1,2}[-]\d{4}[-]\d{4}$/',
				'phone'=>'#^\d{4}[\s\.-]?\d{4}$#');
		if (trim($string) != "") {
			if (preg_match($patterns[$type], $string)) {
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	public static function files($path){
		if (is_dir($path)) {
			if ($dh = opendir($path)) {
				$files = array();
				while (($file = readdir($dh)) !== FALSE) {
					if (!is_dir($path . $file)){
						$files[] = $path.$file;
					}
				}
				closedir($dh);
				return $files;
			}
		}
	}

	public static function log($data){
		echo "<pre>";
		print_r($data);
		echo "</pre>";
	}

	public static function error($method, $message){
		echo "<p>ACORE_OneFile(".$method."): ".$message."</p>";
	}

	public function __call($name,$params){
		self::error("A methods", "Method not found :(");
	}
}

/*
 * CLASS SETTINGS | ACORE
 */
class Settings{

	private $vars = array();
	private static $instance = NULL;

	private function __construct(){
	}

	public static function init()
	{
		if (self::$instance == NULL) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __get($name) {
		if(isset($this->vars[$name])){
			return $this->vars[$name];
		} else {
			A::error("Settings", "Variable [".$name."] not found :(");
		}
	}

	public function __set($name, $value) {
		$this->vars[$name] = $value;
	}
}

/*
 * CLASS TEMPLATE
 */
class Template{

	private $templates = array();

	public function __call($name,$params) {
		if (array_key_exists($name, $this->templates)) {
			return $this->getTemplate($name,$params[0]);
		}else{
			echo "No template!!";
		}
	}

	public function __set($name,$value) {
		$this->templates[$name] = $value;
	}

	private function getTemplate($name_template,$data){
		if(isset($this->templates[$name_template])){
			$template_origin = $this->templates[$name_template];
		}else{
			$template_origin = $name_template;
		}
		$fields = array();
		$values = array();
		foreach ($data as $field => $value ){
			$fields[] = ":".$field;
			if(is_array($value)){
				foreach ($value[1] as $v){
					$nuevos .= $this->getTemplate($value[0], $v);
				}
				$values[] = $nuevos;
			}else{
				$values[] = $value;
			}
		}
		$output  = str_replace($fields, $values, $template_origin);
		return $output;
	}
}

/*
 * CLASE ACORE MAIN
 */
class acore{
	
	private $controllers = array();
	public $vars = NULL;

	public function __construct(){
		if (!session_id()) {
	      session_start();
	    }
	    $this->vars = Settings::init();
	}
	
	private function addModule($nameModule){
		$file_module = "ac".ucfirst($nameModule).".php";
		if(file_exists($file_module)){
			include_once($file_module);
			$classController = $nameModule . "Module";
			$this->controllers[$nameModule] = new $classController;
			return $this->controllers[$nameModule];
		}else{
			A::error("core", "Module [".$nameModule."] not found :(");
			return FALSE;
		}
	}		

    public function __get($name) {
		if (!array_key_exists($name, $this->controllers)) {
            return $this->addModule($name);
        }else{
			return $this->controllers[$name];
        }
    }
    
	public function __call($name,$params){
		A::error("core", "Method [".$name."] not found :(");
	}
}

/*
 * CLASS DATABASE | ACORE
 */
class DatabasePDO extends PDO{

	private static $instance = NULL;
	private $recordSet = NULL;
	private $query = "";
	protected $acore = NULL;

	public function __construct()
	{
		$this->acore = Settings::Init();
		try {
			parent::__construct('mysql:host=' . $this->acore->host . ';dbname=' . $this->acore->database,$this->acore->user, $this->acore->pass);
		} catch(PDOException $e) {
			A::error("database", $e->getMessage());
		}
	}

	public static function Init()
	{
		if (self::$instance == NULL) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/*
	 * QUERY NORMAL
	*
	* (SELECT :fields) | array(field => value)
	*/
	public function queryNormal($sentence,$data){
		return $this->sendQuery($sentence, $data);
	}

	/*
	 * SELECT
	*
	* (SELECT * FROM table WHERE field= :field ORDER BY field ASC LIMIT 0,100) | array(field => value)
	*/
	public function querySelect($table,$data='*',$where='',$fields=array(),$order='',$limit='',$other=''){
		$sentence = "SELECT ";
		 
		if(is_array($data)){
			$sentence .= implode(',', $data);
		}else{
			$sentence .= $data;
		}
		 
		$sentence .= " FROM ".$table;
		$sentence .= ( $where == '' ) ? '' : ' WHERE ' . $where;
		$sentence .= ( $order == '' ) ? '' : ' ORDER BY ' . $order;
		$sentence .= ( $limit == '' ) ? '' : ' LIMIT ' . $limit;
		$sentence .= ( $other == '' ) ? '' : $other;
		$sentence .= ";";

		return $this->sendQuery($sentence, $fields);
	}
	 
	/*
	 * INSERT
	*
	* (INSERT INTO table (fields) as (:fields)) | array(field=> value)
	*/
	public function queryInsert($table,$data){
		$fields = "";
		$values = "";
		$params = array();
		 
		foreach ($data as $field => $value){
			$fields .= $field.",";
			$values .= ":".$field.",";
		}

		$fields = substr($fields, 0,-1);
		$values = substr($values, 0,-1);
		 
		$sentence = "INSERT INTO " . $table ." (".$fields.") VALUES (".$values.");";
		 
		return $this->sendQuery($sentence,$data,$table);
	}

	/*
	 * UPDATE
	*
	* (UPDATE table SET field = :field WHERE field = :field)
	*/
	public function queryUpdate($table,$data,$where,$fields){
		$sentence = "UPDATE " . $table . " SET ";
		foreach ($data as $field => $value){
			$sentence .= $field . " = :". $field . ",";
		}
		$sentence = substr($sentence, 0, -1);
		$sentence .= ' WHERE ' . $where;
		 
		$arrays = array_merge($data,$fields);

		return $this->sendQuery($sentence,$arrays,$table);
	}

	/*
	 * DELETE
	*
	* (DELETE FROM table WHERE field = :field)
	*/
	public function queryDelete($table,$where,$fields){
		$sentence = "DELETE FROM " . $table . ' WHERE ' . $where;
		return $this->sendQuery($sentence, $fields);
	}

	/*
	 * PDO Send Query ('saneadas')
	*/
	private function sendQuery($sentence,$data,$table=NULL){
		$pdos = $this->prepare($sentence);
		if(!empty($data)){
			foreach ($data as $field => $value){
				if(is_numeric( $value )){
					$pdos->bindValue(":".$field, $value, PDO::PARAM_INT);
				}else{
					$pdos->bindValue(":".$field, $value, PDO::PARAM_STR);
				}
			}
		}

		if($pdos->execute()){
			if($sentence[0] == "S"){
				return $pdos->fetchALL(PDO::FETCH_ASSOC);
			}elseif ($sentence[0] == "I") {
				return PDO::lastInsertId();
			}else{
				return TRUE;
			}
		}else{
			A::error("database", "Check query.");
			A::log($sentence);
			A::log($data);
			return FALSE;
		}
	}
}

/*
 * ABSTRACT CLASS MODULE
 */
abstract class AbstractModule{
	protected $model = NULL;
	protected $view = NULL;
	protected $acore = NULL;
	
	public function __construct($activateDB = TRUE){
		$this->acore = Settings::Init();
		$this->view = new Template();
		if($activateDB){
			$this->model = new DatabasePDO;
		}
	}
	
	public function __call($name,$params){
		A::error("module", "Module [".$name."] not found :(");
	}
}