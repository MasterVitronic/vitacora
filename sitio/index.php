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

require_once realpath(dirname(__FILE__)) . '/../config.php';
date_default_timezone_set(default_date_timezone);
setlocale(LC_TIME, locale);
(production)?error_reporting(0):error_reporting(-1);
 
require_once(ROOT . 'lib' . DS . 'class.db.php');
require_once ROOT . 'lib' . DS . 'guachi.php';
require_once ROOT . 'lib' . DS . 'class.controlador.php';
require_once ROOT . 'lib' . DS . 'class.auth.php';
require_once ROOT . 'lib' . DS . 'class.scape_string.php';
require_once ROOT . 'lib' . DS . 'class.guachi.php';
require_once ROOT . 'lib' . DS . 'class.crud.php';
require_once ROOT . 'lib' . DS . 'class.mustache.php';
require_once ROOT . 'lib' . DS . 'class.metadata.php';
require_once ROOT . 'lib' . DS . 'class.cache.php';

/*Iniciamos todas las instancias*/
$cbd            = new ezSQL_sqlite3(ROOT , sqlite_db);
$cbd            ->exec('PRAGMA journal_mode=wal;');
$cbd            ->exec('PRAGMA foreign_keys=ON;');
$crud           = crud::iniciar();
$guachi         = guachi::iniciar();
$limpiador      = limpiador::iniciar();
$controlador    = controlador::iniciar();
$get_modulo     = $controlador->get_modulo();
$auth           = auth::iniciar();
$metatags       = metadata::iniciar();
$cache          =  new MicroCache();
$mustache       =  new Mustache_Engine(array(
    'loader'    => new Mustache_Loader_FilesystemLoader(
                        ROOT . DS . 'vistas',
                        ['extension' => '.html']
    )
));

if (isset($get_modulo['modulo'])) {
    $file_modulo = $get_modulo['modulo'];
    if ($controlador->check_modulo($file_modulo)) {
        require_once(ROOT . 'modulos' . DS . $file_modulo . '.php');
    } else {
        require_once(ROOT . 'modulos' . DS . 'error.php');
    }
}
