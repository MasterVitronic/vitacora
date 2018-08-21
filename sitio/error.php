<?php
/*
            ____                  _     _
           / ___|_   _  __ _  ___| |__ (_)
          | |  _| | | |/ _` |/ __| '_ \| |
          | |_| | |_| | (_| | (__| | | | |
           \____|\__,_|\__,_|\___|_| |_|_|
Copyright (c) 2014  Díaz  Víctor  aka  (Máster Vitronic)
Copyright (c) 2018  Díaz  Víctor  aka  (Máster Vitronic)
<vitronic2@gmail.com>   <mastervitronic@vitronic.com.ve>
*/

define('GUACHI_VERSION','1.8');
define('DS', DIRECTORY_SEPARATOR);/*puto windows*/
define('ROOT', realpath(dirname(__FILE__)) .DS.'..'.DS);
define('tema','aurun');
require_once ROOT . 'lib' . DS . 'guachi.php';
require_once ROOT . 'lib' . DS . 'class.controlador.php';
require_once ROOT . 'lib' . DS . 'class.auth.php';
require_once ROOT . 'lib' . DS . 'class.plantilla.php';
require_once ROOT . 'modulos' . DS . 'error.php';

