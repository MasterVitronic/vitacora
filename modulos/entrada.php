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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_reporting(0); //deshabilito los reportes de error
    /*recolecto los datos y los sanitizo*/
    $limpio = $limpiador->recolectar($_POST, tipo_db, true);    
    if($auth->logIn($limpio->username, $limpio->password)){
        header("Location: /admin/article", true, 301);
        return;
    }
    header("Location: /entrada?".time(), true, 301);
    return;
}

if($auth->isLogged()){
    header("Location: /admin/article", true, 301);
    return;
}

/*Aqui van los datos de la plantilla metadata*/
$meta = [
    'title'             => 'Entrada de Administración | Vitacora',
    'descripcion'       => 'Entrada de administracion a Vitacora',
    'guachi_version'    => GUACHI_VERSION,
    'css'               => [
                        ['css' => 'themes/public/hack/hack.css'],
                        ['css' => 'themes/public/hack/dark.css'],
                        ['css' => 'themes/public/hack/site.css']
    ]
];
/*Aqui van lo script javacript a usar*/
$js = [];
/*@TODO normalizar esta variable*/
$dirTheme = 'public'. DS . public_theme . DS ;
/*La plantilla de la pagina*/
$pagina                 = $mustache->loadTemplate($dirTheme . 'page');
/*La plantilla del metadata*/
$metadata               = $mustache->loadTemplate($dirTheme . 'metadata');

/*No hacer cache*/
$guachi->set_no_cache_header();
/*Finalmente renderizo la pagina*/
print($pagina->render([
            'metadata'      => trim($metadata->render($meta)),
            'header'        => $mustache->loadTemplate($dirTheme .'header'),
            'body'          => $mustache->loadTemplate($dirTheme . 'login/main'),
            'footer'        => $mustache->loadTemplate($dirTheme .'footer'),
            'js'            => $js
        ]
));
