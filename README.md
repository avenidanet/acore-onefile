ACore (One File)
====
##Simple Framework PHP v.1.1.0

![New Acore](http://avenidanet.com/acore/tree_acore.jpg)

Versión ligera del proyecto Acore.

http://www.avenidanet.com

Documentación y proyecto en http://www.avenidanet.com/acore (No habilitado todavía)

Desarrollo orientado a módulos (basados en Model View Controller). 
Cada modulo es llamado ACM (ACore Módulo). 
Además gestiona fácilmente base de datos, templates, llamados a apis, validaciones, etc.

## Modo de uso

Solo es necesario el archivo acore.php, un modulo representado por acNombre.php (en el repositorio se ofrece una demo llamada acMain.php) y el archivo que va hacer uso del acore en este caso index.php.

Los módulos se reconocen por esta nomenclatura acNombre.php.

Se crea una clase con el nombre del archivo, siendo una extensión de AbstractModule

``` php
class acNombre extends AbstractModule{
	//Metodos propios
}
```

Por defecto la conexión a base de datos esta habilitada, entonces antes de la clase hay que configurar los datos para la conexión.

``` php

$config = Settings::Init();
$config->host = 'localhost';
$config->user = 'root';
$config->pass = 'root';
$config->database = 'usuarios';

class acNombre extends AbstractModule{

...

``` 

Se puede desactivar, incluyendo un constructor en el módulo

``` php

	public function __construct(){
		parent::__construct(FALSE);
	}
	
```

Este es un ejemplo de un metodo controlador

``` php

	public function test(){
		echo "Hello Acore";
	}

```

Para utilizarlo en el proyecto (index.php u otro)

``` php

include 'acore.php';

//Ya la instancia de $acore ha sido creada en el include
$acore->nombre->test();

```

Ejemplo de un metodo controlador con model (tabla usuarios), utilizando los metodos estaticos de la clase A para desplegar la información tomada de la consulta select a la base de datos "Usuarios"

``` php

	public function testDB(){
		echo "Hello Acore with DB";
		$data = $this->model->querySelect("usuarios");
		A::log($data);
	}

```

Se pueden crear cuantos modulos se necesiten, cada uno gestiona un controller, model y view totalmente independiente.
``` php
include 'acore.php';

$acore->modulo1->test(); //Metodo en acModulo1.php

$acore->modulo2->test(); //Metodo en acModulo2.php

```

## Metodos del controller

Acceso a las variables globales dentro de la clase módulo

``` php
	
	//Asignar valor
	$this->acore->var1 = 'Hello';
	
	//Obtener valor
	A::log($this->acore->var1);

```
o de forma directa.
``` php

include 'acore.php';

//Asignar valor
$this->vars->var1 = 'Hello';

//Obtener valor
A::log($this->vars->var1);

```
Acceso a model y view.
``` php
	$this->model->metodo;
	$this->view->metodo;
```

## Metodos del model

Realizar un query en base de datos.
``` php
	//(SELECT :fields) | array(field => value)
	$this->model->queryNormal('SELECT * FROM tabla WHERE id = :campo',array('campo'=>'valor'));
```
Realizar una consulta
``` php
	//(SELECT * FROM table WHERE field= :field ORDER BY field ASC LIMIT 0,100) | array(field => value)
	$this->model->querySelect($table,'*',$where,$fields=array(),$order,$limit,$other);
	//or
	$this->model->selectIn_nametable('*',$where,$fields=array(),$order,$limit,$other);
	// Example: $this->model->selectIn_products();
```
Realizar una consulta en varias tablas
``` php
	//(SELECT * FROM table1 INNER JOIN table2 ON table1.id = table2.id INNER JOIN table3 ON table2.id2 = table3.id3 ;)
	$tables = array('table_root','table2'=>'id','table3'=>'id2,id3');
	$this->model->querySelect($tables);
```
Insertar datos en la base de datos.
``` php
	//(INSERT INTO table (fields) as (:fields)) | array(field=> value)
	$this->model->queryInsert($table,$data);
	//or
	$this->model->insertIn_nametable($data); // Example: $this->model->insertIn_products($data);
```
Actualizar datos en la base de datos.
``` php
	//(UPDATE table SET field = :field WHERE field = :field)
	$this->model->queryUpdate($table,$data,$where,$fields);
	//or
	$this->model->updateIn_nametable($data,$where,$fields);
```
Borrar datos en la base de datos.
``` php
	//(DELETE FROM table WHERE field = :field)
	$this->model->queryDelete($table,$where,$fields);
	//or
	$this->model->deleteIn_nametable($where,$fields);
```

Si se desea solo ver la consulta sin realizarla, se puede activar el modo debug.
``` php
	$this->model->debug(); //Eliminar después de su uso
	//Antes de la o las consultas a visualizar.
	$this->model->selectIn_products();
```

## Metodos del view

Crear un template:
``` php
$this->view->input = "[:id]<input type='text' />[:email] [:fecha] [:identificacion]<br/>";
```
Usar un template, $data es un arreglo con los registros y cada unos de los campos a reemplazar [:campo]
``` php
//Se repite tantas veces como registros se encuentren.
$this->view->input($data);
```

## Metodos generales

Crear caché
``` php
	//Cache de un minuto (60s), por defecto si no se coloca este valor.
	A::cache_begin('identificador',60);
	
	//Contenido.
	
	A::cache_end('identificador');
```

Variables globales del proyecto.

``` php

$config = Settings::Init();
//Asignar valor
$config->host = 'localhost';

//Obtener valor
$config->host;

```

Función para incluir javascript (CDN o directorio) y css en el proyecto.

Opciones con JS: jquery, angular, swfobject, validate, gmaps, acore.
Además si se agrega el segundo parametro (directorio) incluirá todos los js que se encuentren en la misma.

Con CSS solo se debe agregar el directorio.
``` php
<head>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
  <title>README</title>
  <?php A::css('css/')?>
</head>
<body>
	<div id="container">
	  
	</div>
	<?php A::script('jquery, angular','js/')?>
</body>
```
Log, muestra de arreglos o datos.

``` php
$array_fruits = array('orange','banana');
A::log($array_fruits);
```

Error, mostrar un error para realizar cualquier tipo de debug.

``` php
A::error('lugar donde se produce el error','descripcion del error');
```

Validar datos.
Tipos: text, number, phone, email, name, id

``` php
A::validate('texto a evaluar','tipo');
```

Angular parametros, devuelve en un arreglo valores pasados por el metodo http de angular
``` php
$data = A::ng_params();
```

Este desarrollo esta bajo licencia MIT.

The MIT License (MIT)

Copyright (c) 2006-2013 Brian Salazar [www.avenidanet.com]

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

http://mit-license.org