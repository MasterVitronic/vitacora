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

/*la clase posts*/
require_once(ROOT . 'lib' . DS . 'class.posts.php');
/*inicializo la clase posts*/
$posts = posts::iniciar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /*recolecto los datos y los sanitizo*/
    $limpio = $limpiador->recolectar($_POST, tipo_db, true);
    /*paso los datos a la clase crud*/
    $crud->datos($limpio);
    /*errores vacios de momento*/
    $erros = [];
    /*si falta el nombre registro el mensaje*/
    if (!$limpio->name) {
        array_push($erros,['warning' => 'Falta nombre']);
    }
    /*si falta el mensaje registro el mensaje*/
    if (!$limpio->comment) {
        array_push($erros,['warning' => 'Falta Mensaje']);
    }
    /*Si faltan datos envio el Warning*/
    if ( empty($limpio->name) or empty($limpio->comment) ) {
        $posts->Warnings($erros);
        /*guardo los datos de envio y los envio a la clase posts*/
        $posts->setNameOfComment($limpio->name);
        $posts->setComment($limpio->comment);
    }else{
        $crud->crear_insert('comments');
        if ($crud->query() === false) {
            /*No se pudo registrar el comentario, registro el warning*/
            array_push($erros,['warning' => 'ERROR: no se pudo registrar tu comentario']);
            $posts->Warnings($erros);
        }
    }
}

/*establece los resultados por pagina*/
$posts->resultPerPage(3);

$lang = 'es';
$metatags->setSiteName(siteName);
$metatags->setAuthor('Máster Vitronic');
$metatags->setCanonicalUrl('https://vitronic.me/posts');
$metatags->setTitle('Bitácora de Máster Vitronic');
$metatags->setDescription('Bitácora personal de Máster Vitronic');
$metatags->setLang('ES_es');
$metatags->setGoogleToken('5dXdklSHSv7lvP6zLqMM6orNsubDgnI0M24jdnnYnQw');

$metatags->addTypeSchema('Person');
$metatags->addTypeSchema('Organization');
$metatags->addTypeSchema('Site');

$metatags->setPageType('website');
$metatags->setTwiter('@MasterVitronic');
$metatags->setImage('https://vitronic.me/img/Master-Vitronic.png');

$metatags->addSocialCard('OpenGraph');
$metatags->addSocialCard('Twiter');
$metatags->addSocialCard('Google');

$metatags->addCss('themes/public/hack/hack.css');
$metatags->addCss('themes/public/hack/dark.css');
$metatags->addCss('themes/common/geshi/geshi-dark-scheme.css');
$metatags->addCss('themes/public/hack/site.css');

/*Aqui van lo script javacript a usar*/
$js = [
    ['js'               => 'themes/public/hack/app.js']
];
/*@TODO normalizar esta variable*/
$dirTheme = 'public'. DS . public_theme . DS ;
/*La plantilla de la pagina*/
$pagina                 = $mustache->loadTemplate($dirTheme . 'page');
/*La plantilla del metadata*/
$metadata               = $mustache->loadTemplate($dirTheme . DS . 'posts/metadata');
/*En este caso el body va aqui*/
$body                   = $mustache->loadTemplate($dirTheme . $posts->getTemplate());

/*Finalmente rederizo la pagina*/
print($pagina->render([
            'lang'      => $lang,
            'body'      => $body->render($posts->getContent()),
            'metadata'  => trim($metadata->render($metatags->getMetadata())),
            'header'    => $mustache->loadTemplate($dirTheme . 'header'),
            'footer'    => $mustache->loadTemplate($dirTheme . 'footer'),
            'js'        => $js
        ]
));
