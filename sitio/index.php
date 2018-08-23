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
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath(dirname(__FILE__)) .DS.'..'.DS);
define('tipo_db','sqlite');
define('sqlite_db','vitacora.db');
define('modulo_inicio','inicio');
define('public_theme','hack');
define('admin_theme' ,'pure');
define('site_url','https://vitronic.me');
define('salt','@VNX@P@st3Ch@r3');
define('default_date_timezone','America/Caracas');
define('locale','es_ES.UTF-8');
define('format_fecha','d/m/Y');
define('format_date_time','d/m/Y h:i:s A');
define('format_time','h:i A');
define('locale_money','es_VE');
define('char_remove_money','Bs.');
define('simbolo_money','Bs');
define('guachi_production','no');
date_default_timezone_set(default_date_timezone);
setlocale(LC_TIME, locale);
(guachi_production === 'no')?error_reporting(-1):error_reporting(0);
$header="X-Powered-By: Guachi (Lightweight and very simple web "
        ."development framework of Vitronic) v".GUACHI_VERSION;
 
require_once(ROOT . 'lib' . DS . 'class.db.php');
require_once ROOT . 'lib' . DS . 'guachi.php';
require_once ROOT . 'lib' . DS . 'class.controlador.php';
require_once ROOT . 'lib' . DS . 'class.auth.php';
require_once ROOT . 'lib' . DS . 'class.scape_string.php';
require_once ROOT . 'lib' . DS . 'class.guachi.php';
require_once ROOT . 'lib' . DS . 'class.crud.php';
require_once ROOT . 'lib' . DS . 'class.mustache.php';

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
$mustache       =  new Mustache_Engine(array(
    'loader'    => new Mustache_Loader_FilesystemLoader(
                        ROOT . DS . 'vistas',
                        ['extension' => '.html']
    )
));
header($header);
if (isset($get_modulo['modulo'])) {
    $file_modulo = $get_modulo['modulo'];
    if ($controlador->check_modulo($file_modulo)) {
        require_once(ROOT . 'modulos' . DS . $file_modulo . '.php');
    } else {
        require_once(ROOT . 'modulos' . DS . 'error.php');
    }
}
