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
    'title'             => 'Administración de articulos | '  ,
    'guachi_version'    => GUACHI_VERSION,
    'css'               => [
                        ['css' => 'admin/themes/mdl/material.cyan-light_blue.min.css'],
                        ['css' => 'admin/themes/mdl/material-icons.css'],
                        ['css' => 'admin/themes/mdl/styles.css'],
                        ['css' => 'admin/themes/mdl/article/main.css']
    ]
];
/*Aqui van lo script javacript a usar*/
$js = [
    ['js'               => 'common/jquery-3.3.1.min.js'],
    ['js'               => 'common/material.min.js'],
    ['js'               => 'admin/article/article.js']
];

/*La plantilla de la pagina*/
$pagina                 = $mustache->loadTemplate('admin/themes/mdl/page');
/*La plantilla del metadata*/
$metadata               = $mustache->loadTemplate('admin/themes/mdl/metadata');

/*En este caso el body va aqui*/
$body                   = $mustache->loadTemplate('admin/themes/mdl/article/main');

/*Finalmente renderizo la pagina*/
print($pagina->render([
            'metadata'  => $metadata->render($meta),
            'body'      => $body->render([]),
            'footer'    => $mustache->loadTemplate('admin/themes/mdl/footer'),
            'js'        => $js
        ]
));


