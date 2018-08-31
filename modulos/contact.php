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

/*La marca*/
$brand                  = 'Máster Vitronic';
/*lenguaje de la pagina*/
$lang                   = 'ES_es';
/*la url de la pagina*/
$url                    = 'https://vitronic.me/';

/*token de verificacion de google*/
$google_verification    = '5dXdklSHSv7lvP6zLqMM6orNsubDgnI0M24jdnnYnQw';
/*descripcion de la pagina*/
$descripcion            = 'Contacto y Redes Sociales de Máster Vitronic';

/*schema.org*/
$name                   = 'Díaz Devera Víctor Diex Gamar';
$image                  = 'https://vitronic.me/img/Master-Vitronic.png';
$telephone              = '+58-288-442-0387';
$email                  = "vitronic2@gmail.com";
$job                    = 'Software Engineer';
$streetAddress          = 'Avenida Valmore Rodríguez, Upata, Bolívar';
$addressLocality        = 'Upata';
$addressRegion          = 'VE-F';
$addressCountry         = 'VE';
$postalCode             = '8052';

/*nombre de la pagina*/
$site_name              = $name;
/*la direccion de twiter del autor*/
$twitter                = '@MasterVitronic';
/*la la direccion de los posts del autor en G+*/
$google_p_author        = 'https://plus.google.com/+VíctorDiexDíazDevera/posts';
/*el perfil de G+ del autor*/
$google_p_publisher     = 'https://plus.google.com/+VíctorDiexDíazDevera';
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
$dateModified           = '2018-08-31 03:00';


/*Aqui van los datos de la plantilla metadata*/
$meta = [
    'title'             => 'Contacto y Redes Sociales | Máster Vitronic',
    'descripcion'       => $descripcion,
    'author'            => $brand,
    'canonical_url'     => $url.'contact',
    'guachi_version'    => GUACHI_VERSION,
    'lang'              => $lang,
    'css'               => [
                        ['css' => 'themes/public/hack/hack.css'],
                        ['css' => 'themes/public/hack/dark.css'],
                        ['css' => 'themes/public/hack/site.css']
    ],
    /*SEO*/
    'google_verification'=> $google_verification,
    'schemas'   =>[
                    'person'        =>[
                        'name'      => $name,
                        'url'       => $url,
                        'image'     => $image,
                        'gender'    => 'Male',
                        'telephone' => $telephone,
                        'email'     => $email,
                        'jobTitle'  => $job,
                        'streetAddress'  =>$streetAddress,
                        'addressLocality'=>$addressLocality,
                        'addressRegion'  =>$addressRegion,
                        'addressCountry' =>$addressCountry,
                        'postalCode'     =>$postalCode,
                        'sameAs'    => 'yes',
                        'networks'  => [
                            ['net'  => '"'.$url_facebook.'",' ],
                            ['net'  => '"'.$url_youtube.'",' ],
                            ['net'  => '"'.$url_linkedin.'",' ],
                            ['net'  => '"'.$url_googlep.'",' ],
                            ['net'  => '"'.$url_twitter.'"' ]
                        ]
                ],
                'organization' =>[
                    'name'              => $brand,
                    'url'               => $url,
                    'image'             => $image,
                    'telephone'         => $telephone,
                    'contactType'       => 'Customer Service',
                    'availableLanguage' => 'Spanish'
                ],
                'webSite' =>[
                    'url' => $url
                ]
    ],
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
    ['js'               => 'themes/public/hack/app.js']
];
/*@TODO normalizar esta variable*/
$dirTheme = 'public'. DS . public_theme . DS ;
/*La plantilla de la pagina*/
$pagina                 = $mustache->loadTemplate($dirTheme . 'page');
/*La plantilla del metadata*/
$metadata               = $mustache->loadTemplate($dirTheme . 'metadata');
/*En este caso el body va aqui*/
$body                   = $mustache->loadTemplate($dirTheme . 'contact/main');
/*seteo una cache de 1 hora*/
$guachi->set_cache_header();
/*Finalmente renderizo la pagina*/
print($pagina->render([
            'lang'          => 'es',
            'metadata'      => trim($metadata->render($meta)),
            'header'        => $mustache->loadTemplate($dirTheme . 'header'),
            'body'          => $body->render([
                'inLanguage'    => $lang,
                'datePublished' => date("c",strtotime($datePublished)),
                'dateModified'  => date("c",strtotime($dateModified)),
            ]),
            'footer'        => $mustache->loadTemplate($dirTheme . 'footer'),
            'js'            => $js
        ]
));
