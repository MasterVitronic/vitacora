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

/*la clase articles*/
require_once(ROOT . 'lib' . DS . 'class.articles.php');
/*inicializo la clase articles*/
$article = article::iniciar();
/*seteo el request*/
$article->setRequest();

/*acciones al guardar*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {  
    require_once(ROOT . 'lib' . DS . 'class.parsedown.php');
    /*recolecto los datos y los sanitizo*/
    $limpio = $limpiador->recolectar($_POST, tipo_db, true);
    /*paso los datos a la clase crud*/
    $crud->datos($limpio);
    /*inicializo el convertidor de Markdown a HTML*/
    $md2html = new ParsedownExtra();
    $e = ['url']; //verificar si existen (campos unicos)
    $n = ['title','articleBody','url']; //no deben ser nulos
    $nulo = $crud->check('nulo', $n, false, false, false);
    if ($nulo) {
        $article->setDefaultWarning($nulo,'null');
    }
    $exist = false;
    if($limpio->id_post){
        $exist = $crud->check('existe', $e, 'posts','id_post',$limpio->id_post);
    }else{
        $exist = $crud->check('existe', $e, 'posts', false, false);
    }
    if ($exist) {
        $article->setDefaultWarning($exist,'exist');
    }
    $article->dataToSave = [
            'title'         => $limpio->title,
            'url'           => $limpio->url,
            'articleBody'   => $limpio->articleBody,
            'id_post'       => ($limpio->id_post) ? $limpio->id_post : '',
    ];
    $tags       = isset($_POST['tags'])       ? $_POST['tags']       : '' ;
    $categories = isset($_POST['categories']) ? $_POST['categories'] : '' ;
    $csrf = $auth->csrfIsValid($limpio->csrf);
    if (!$csrf) {
        $article->setWarning('AVISO, csrf detectado, si considera que esto es un error, proceda, ESTA AVISADO!');
    }
    if (!$nulo and !$exist and $csrf) {
        $limpio->articleSrc  = $limpio->articleBody;
        if($limpio->normalice_md === 't'){
            $limpio->articleSrc = $article->html2md($md2html->text($limpio->articleBody));
        }
        $limpio->articleBody = $md2html->text($limpio->articleBody) ;
        /*nueva publicacion*/
        if(!$limpio->id_post){
            if ($article->savePost($limpio) === true) {
                $id_post        = $cbd->lastInsertRowID();
                $saveTags       = $article->saveTags($tags,$id_post);
                $saveCategories = $article->saveCategories($categories,$id_post);
                if( $saveTags and $saveCategories ){
                    $cbd->close(); /*para evitar el bloqueo de sqlite*/
                    header("Location: /admin/article", true, 301);
                }
            }else{
                $article->setWarning('Error, Will Robinson, ERROR!');
            }
        }else{
            /*actualizar publicacion*/
            if ($article->savePost($limpio,$limpio->id_post) === true) {
                $saveTags       = $article->saveTags($tags,$limpio->id_post);
                $saveCategories = $article->saveCategories($categories,$limpio->id_post);
                if( $saveTags and $saveCategories ){
                    $cbd->close(); /*para evitar el bloqueo de sqlite*/
                    $cache->clear('/posts');
                    $cache->clear('/posts/'.$limpio->url);/*borro la cache de esta publicacion*/
                    header("Location: /admin/article", true, 301);
                }
            }else{
                $article->setWarning('Error, Will Robinson, ERROR!');
            }
        }
    }
}

/*togglear el estatus de la publicacion*/
if( $article->request->mode === 'toggle' ){
    if($auth->csrfIsValid($article->request->csrf)){
        $cache->clear('/posts');/*limpio la cache del paginador*/
        $article->toggleDraft($article->request->id);
    }
    header("Location: /admin/article", true, 301);
    return;
}
/*seteo el csrf*/
$auth->setCsrf();
/*Aqui van los datos de la plantilla metadata*/
$meta = [
    'title'             => 'Administración de articulos | '  ,
    'guachi_version'    => GUACHI_VERSION,
    'css'               => [
                        ['css' => 'themes/admin/moscow/common/moscow.css'],
                        ['css' => 'themes/common/multi.min.css'],
                        ['css' => 'themes/common/simplemde.min.css'],
                        ['css' => 'themes/common/font-awesome.css'],
                        ['css' => 'themes/admin/moscow/common/common.css'],
                        ['css' => 'themes/admin/moscow/article/main.css']
    ]
];
/*Aqui van lo script javacript a usar*/
$js = [
    ['js'               => 'themes/common/multi.min.js'],
    ['js'               => 'themes/common/simplemde.min.js'],
    ['js'               => 'themes/admin/moscow/article/main.js']
];

/*@TODO normalizar esta variable*/
$dirTheme = 'admin'. DS . admin_theme . DS ;
/*La plantilla de la pagina*/
$pagina                 = $mustache->loadTemplate($dirTheme . 'page');
/*La plantilla del metadata*/
$metadata               = $mustache->loadTemplate($dirTheme . 'metadata');
/*En este caso el body va aqui*/
$body                   = $mustache->loadTemplate($dirTheme . 'article/main');
/*el menu*/
$menu                   = $mustache->loadTemplate($dirTheme . 'menu');

$content                = $mustache->loadTemplate($article->getTemplate());

/*Finalmente renderizo la pagina*/
print($pagina->render([
            'metadata'  => $metadata->render($meta),
            'body'      => $body->render([
                'content'  => $content->render($article->getContent() + [ 'csrf' => $auth->getCsrf() ]  ),
                'menu'     => $mustache->loadTemplate($dirTheme . 'menu'),
                'footer'   => $mustache->loadTemplate($dirTheme . 'footer')
            ]),
            'js'        => $js
        ]
));


