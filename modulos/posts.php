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
$posts->resultPerPage(1);

/*La marca*/
$brand                  = 'Máster Vitronic';
/*lenguaje de la pagina*/
$lang                   = 'ES_es';
/*la url de la pagina*/
$url                    = 'https://vitronic.me/';

/*token de verificacion de google*/
$google_verification    = '5dXdklSHSv7lvP6zLqMM6orNsubDgnI0M24jdnnYnQw';
/*descripcion de la pagina*/
$descripcion            = 'Bitácora personal de Máster Vitronic';

/*imagen*/
$image                  = 'https://vitronic.me/img/Master-Vitronic.png';
/*nombre de la pagina*/
$site_name              = 'Bitácora de Máster Vitronic';
/*la direccion de twiter del autor*/
$twitter                = '@MasterVitronic';
/*el perfil de G+ del autor*/
$google_p_publisher     = 'https://plus.google.com/+VíctorDiexDíazDevera';
/*la la direccion de los posts del autor en G+*/
$google_p_author        = $google_p_publisher.'/posts';
/*tipo de pagina*/
$page_type              = 'website';


/*redes sociales para el schema.org*/
$url_facebook           = 'https://www.facebook.com/MasterVitronic';
$url_youtube            = 'https://www.youtube.com/channel/UClT_C_Bp9gJlK-0-eGCS9Hg';
$url_linkedin           = 'https://www.linkedin.com/in/Master-Vitronic';
$url_googlep            = $google_p_publisher;
$url_twitter            = 'https://twitter.com/MasterVitronic';

/*fechas*/
$datePublished          = '2018-08-17 00:00';
$dateModified           = '2018-08-20 12:51';


/*Aqui van los datos de la plantilla metadata*/
$meta = [
    'title'             => $site_name,
    'descripcion'       => $descripcion,
    'author'            => $brand,
    'canonical_url'     => $url.'posts',
    'guachi_version'    => GUACHI_VERSION,
    'lang'              => $lang,
    'css'               => [
                        ['css' => 'themes/hack/hack.css'],
                        ['css' => 'themes/hack/dark.css'],
                        ['css' => 'site.css']
    ],
    'google_verification'=> $google_verification,
    /*SEO*/
    'social_card' =>[
                    'og_type'           => $page_type,
                    'twitter'           => $twitter,
                    'image_card'        => $image,
                    'site_name'         => $site_name,
                    'updated_time'      => date("c",strtotime($dateModified)),
                    'google_p_author'   => $google_p_author,
                    'google_p_publisher'=> $google_p_publisher
    ]
];
/*Aqui van lo script javacript a usar*/
$js = [
    ['js'               => 'app.js']
];
/*La plantilla de la pagina*/
$pagina                 = $mustache->loadTemplate('pagina');
/*La plantilla del metadata*/
$metadata               = $mustache->loadTemplate('metadata');
/*En este caso el body va aqui*/
$body                   = $mustache->loadTemplate($posts->getTemplate());

//print_r($posts->getPages());

function comprimir($buffer) {
    $busca = array('/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s');
    $reemplaza = array('>', '<', '\\1');
    return preg_replace($busca, $reemplaza, $buffer);
}

/*Finalmente rederizo la pagina*/
print($pagina->render([
            'lang'          => $lang,
            'metadata'      => trim($metadata->render($meta)),
            'header'        => $mustache->loadTemplate('header'),
            'body'          => $body->render($posts->getContent()),
            'footer'        => $mustache->loadTemplate('footer'),
            'js'            => $js
        ]
));
