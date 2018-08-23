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


/*Router*/
if (isset($get_modulo['arg'])) {
    $file = $get_modulo['arg'];
    $dir = ROOT . 'modulos' . DS . 'admin' ;
    if (is_file($dir . DS . $file . '.php') === true) {
        require_once($dir . DS . $file . '.php');
        return;
    } else {
        require_once( ROOT . DS . 'modulos' . DS . 'error.php');
        return;
    }
}