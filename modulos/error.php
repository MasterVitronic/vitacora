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

/*Aqui van los datos de la plantilla metadata*/
$meta = [
    'tittulo'           => 'Pagina no encontrada',
    'descripcion'       => 'Pagina no encontrada',
    'keywords'          => 'no encontrada, 404',
    'guachi_version'    => GUACHI_VERSION,
    'css'               => [
                        ['css' => 'comun.css'],
                        ['css' => 'modulos/error/error.css']
    ]
];
/*Aqui van lo script javacript a usar*/
$js = [
    ['js'               => 'jquery-1.11.2.js'],
    ['js'               => 'comun.js'],
    ['js'               => 'modulos/error/error.js']
];
/*La plantilla de la pagina*/
$pagina                 = $mustache->loadTemplate('pagina');
/*La plantilla del metadata*/
$metadata               = $mustache->loadTemplate('metadata');
/*el header 404*/
header("HTTP/1.0 404 Not Found");
/*Finalmente renderizo la pagina*/
echo $pagina->render([
            'metadata'  => $metadata->render($meta),
            'body'      => $mustache->loadTemplate('modulos/error/error'),
            'footer'    => $mustache->loadTemplate('footer'),
            'js'        => $js
        ]
);
