<?php
/**
* ACore OneFile v.1.1.0
*
* Simple framework php
*
* @author Brian Salazar [Avenidanet]
* @link http://www.avenidanet.com
* @copyright Brian Salazar 2006-2013
* @license http://mit-license.org
* 
*/

function __autoload($className) {
	A::error("core", "Class [".$className."] not found :(");
}

/*
 * CLASE A | ACORE
 */
class A{
	
	private function __construct(){
	}
	
	public static function cache_begin($name,$cachetime=60){
		$cachefile = 'cached-'.$name.'.html';
		
		if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile)) {
			include($cachefile);
			exit;
		}
		ob_start();		
	}
	
	public static function cache_end($name){
		$cachefile = 'cached-'.$name.'.html';
		$cached = fopen($cachefile, 'w');
		fwrite($cached, ob_get_contents());
		fclose($cached);
		ob_end_flush();
	}
	
	public static function script($data,$load=''){
		$CDN = array(	'acore'=>'//apps.avenidanet.com/acore/acore.min.js',
						'jquery'=>'//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js',
						'angular'=>'//cdnjs.cloudflare.com/ajax/libs/angular.js/1.1.5/angular.min.js',
						'swfobject'=>'//cdnjs.cloudflare.com/ajax/libs/swfobject/2.2/swfobject.js',
						'validate'=>'//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.11.1/jquery.validate.min.js',
						'gmaps'=>'//maps.googleapis.com/maps/api/js?v=3&sensor=false',
						'tween'=>'//cdnjs.cloudflare.com/ajax/libs/gsap/1.10.3/TweenMax.min.js');
		$jss = explode(',',$data);
		foreach ($jss as $js){
			echo '<script src="'.$CDN[$js].'"></script>';
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
	//Aporte Marvin Solano
	public static function css($directory){
		if($directory != ''){
			$css = self::files($directory);
			foreach ($css as $fcss){
				if(substr($fcss,-4) == ".css"){
					echo '<link rel="stylesheet" href="'.$fcss.'" />';
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
			A::error("settings", "Variable [".$name."] not found :(");
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
			if(isset($params[1])){
				return $this->getTemplate($name,$params[0],FALSE);
			}elseif(isset($params[0])){
				echo $this->getTemplate($name,$params[0],TRUE);
			}else{
				echo $this->templates[$name];
			}
		}else{
			A::error("template", "Template [".$name."]  not found :(");
		}
	}

	public function __set($name,$value) {
		$this->templates[$name] = $value;
	}

	private function getTemplate($name_template,$data,$print){
		$output = '';
		foreach ($data as $d){
			$fields = array();
			$values = array();
			foreach ($d as $field => $value ){
				$fields[] = "[:".$field."]";
				$values[] = $value;
			}
			$output  .= str_replace($fields, $values, $this->templates[$name_template]);
		}
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
			require_once($file_module);
			$classController = "ac".$nameModule;
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
	private $debug = FALSE;

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
	
	public function debug(){
		$this->debug = TRUE;
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
	public function querySelect($tables,$data='*',$where='',$fields=array(),$order='',$limit='',$other=''){
		$sentence = "SELECT ";
		 
		if(is_array($data)){
			$sentence .= implode(',', $data);
		}else{
			$sentence .= $data;
		}
		
		if(is_array($tables)){
			$num_table = 1;
			foreach ($tables as $table_name => $table_field ){
				if($num_table == 1){
					$table = $table_field;
					$table_name = $table_field;
				}else{
					$table_field = explode(',',$table_field);
					$table_field[1] = ($table_field[1])?$table_field[1]:$table_field[0];
					$join .= " INNER JOIN ".$table_name." ON ".$table_ant.".".$table_field[0]." = ".$table_name.".".$table_field[1]." ";
				}
				$table_ant = $table_name;
				$num_table++;
			}
		}else{
			$table = $tables;
		}
		
		$sentence .= " FROM ".$table;
		$sentence .= ( $join == '') ? '' : $join;
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
		 
		return $this->sendQuery($sentence,$data);
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

		return $this->sendQuery($sentence,$arrays);
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
	private function sendQuery($sentence,$data){
		if($this->debug){
			A::error("database", "Debug Mode [".$sentence."]");
			A::log($data);
			return FALSE;
		}else{		
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
				A::error("database", "Check query [".$sentence."]");
				A::log($data);
				return FALSE;
			}
		}	
	}
	
	/*
	 * Dynamic query
	* insertIn_products($data) = queryInsert('products',$data);
	*/
	public function __call($name,$params){
		$methods = explode('In_',$name);
		if(count($methods) == 2){
			array_unshift($params,$methods[1]);
			$method = 'query'.ucwords($methods[0]);
			return call_user_func_array(array($this,$method),$params);
		}else{
			A::error("database", "Method [".$name."] not found :(");
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

$acore = new acore;