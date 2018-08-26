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
/*la clase taxonomy*/
require_once(ROOT . 'lib' . DS . 'class.taxonomy.php');
/*inicializo la clase taxonomy*/
$taxonomy = taxonomy::iniciar();
/*seteo el request*/
$taxonomy->setRequest();

/*acciones al guardar*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {  
    error_reporting(0); //deshabilito los reportes de error
    /*recolecto los datos y los sanitizo*/
    $limpio = $limpiador->recolectar($_POST, tipo_db, true);
    /*paso los datos a la clase crud*/
    $crud->datos($limpio);
    $limpio->type = ($taxonomy->request->mode) ? $taxonomy->request->mode : $limpio->type;
    $e = ($limpio->type === 'tags')?['tag']:['category']; //verificar si existen (campos unicos)
    ($limpio->type === 'tags') ? $limpio->tag = $limpio->name : $limpio->category = $limpio->name;
    $n = ['name','type']; //no deben ser nulos
    $nulo = $crud->check('nulo', $n, false, false, false);
    if ($nulo) {
        $taxonomy->setDefaultWarning($nulo,'null');
    }
    $exist = false;
    if($taxonomy->request->id){
        $id = ($taxonomy->request->mode==='tags') ? 'id_tag':'id_category';
        $exist = $crud->check('existe', $e, $limpio->type, $id, $taxonomy->request->id);
    }else{
        $exist = $crud->check('existe', $e, $limpio->type, false, false);
    }
    if ($exist) {
        $taxonomy->setDefaultWarning($exist,'exist');
    }
    $taxonomy->dataToSave=['name' => $limpio->name,'type' => $limpio->type];
    if (!$nulo and !$exist) {
        if($taxonomy->request->id){
            /*actualizar*/
            if($taxonomy->saveTaxonomy($limpio->type,$limpio->name,'update')){
                $cbd->close(); /*para evitar el bloqueo de sqlite*/
                header("Location: /admin/taxonomy", true, 301);
                return;
            }else{
                $taxonomy->setWarning('Error, Will Robinson, ERROR!');
            }
        }else{
            /*nuevo*/
            if($taxonomy->saveTaxonomy($limpio->type,$limpio->name)){
                $cbd->close(); /*para evitar el bloqueo de sqlite*/
                header("Location: /admin/taxonomy", true, 301);
                return;
            }else{
                $taxonomy->setWarning('Error, Will Robinson, ERROR!');
            }
        }
    }
}


/*Aqui van los datos de la plantilla metadata*/
$meta = [
    'title'             => 'Administración de Taxonomia | '  ,
    'guachi_version'    => GUACHI_VERSION,
    'css'               => [
                        ['css' => 'themes/admin/moscow/common/moscow.css'],
                        ['css' => 'themes/admin/moscow/common/common.css']
    ]
];
/*Aqui van lo script javacript a usar*/
$js = [
];

/*@TODO normalizar esta variable*/
$dirTheme = 'admin'. DS . admin_theme . DS ;
/*La plantilla de la pagina*/
$pagina                 = $mustache->loadTemplate($dirTheme . 'page');
/*La plantilla del metadata*/
$metadata               = $mustache->loadTemplate($dirTheme . 'metadata');
/*En este caso el body va aqui*/
$body                   = $mustache->loadTemplate($dirTheme . 'taxonomy/main');
/*el menu*/
$menu                   = $mustache->loadTemplate($dirTheme . 'menu');
/*el contenido*/
$content                = $mustache->loadTemplate($taxonomy->getTemplate());
/*Finalmente renderizo la pagina*/
print($pagina->render([
            'metadata'  => $metadata->render($meta),
            'body'      => $body->render([
                'content'  => $content->render($taxonomy->getContent()),
                'menu'     => $mustache->loadTemplate($dirTheme . 'menu'),
                'footer'   => $mustache->loadTemplate($dirTheme . 'footer')
            ]),
            'js'        => $js
        ]
));


