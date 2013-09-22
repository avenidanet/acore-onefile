ACore (One File) | Simple Framework PHP v.5.1.0
=====

Versión ligera del proyecto Acore.

Este desarrollo esta bajo licencia MIT.

## Modo de uso

Los módulos se reconocen por esta nomenclatura acNombre.php | Equivalente a los módulos originales de ACore.

Se crea una clase con el nombre del módulo + "Module" siendo una extensión de AbstractModule

``` php
class nombreModule extends AbstractModule{
	//Metodos propios
}
```

Por defecto la conexión a base de datos esta habilitada, entonces antes de la clase hay configurar los datos para la conexión.

``` php

$config = Settings::Init();
$config->host = 'localhost';
$config->user = 'root';
$config->pass = 'root';
$config->database = 'usuarios';

class nombreModule extends AbstractModule{

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

Para utilizarlo en el proyecto

``` php

include 'acore.php';
$app = new acore;
$app->nombre->test();

```

Ejemplo de un metodo controlador con model, utilizando los metodos estaticos de la clase A para desplegar la info tomada de la consulta select a la base de datos "Usuarios"

``` php

	public function testDB(){
		echo "Hello Acore with DB";
		$data = $this->model->querySelect("usuarios");
		A::log($data);
	}

```



Copyright (c) 2006-2013 Brian Salazar [www.avenidanet.com]

Permission is hereby granted, free of charge, to any
person obtaining a copy of this software and associated
documentation files (the "Software"), to deal in the
Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the
Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice
shall be included in all copies or substantial portions of
the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY
KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR
OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

http://mit-license.org