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
/*camelCase  :-) */
class posts {
    
    private $cbd;
    private $guachi;
    private $controlador;
    private $request;
    private $limpiador;
    private $limit;
    private $perpage = 3;
    private $Warnings ;
    private $name = '';
    private $commentPost = '';
    private $metatags;

    /**
     * Instancia para el patrón de diseño singleton (instancia única)
     * @var object instancia
     * @access private
     */
    private static $instancia = null;

    /**
     * __construct
     *
     * Constructor de la clase
     *
     * @access public
     *
     */
    public function __construct() {
        global $cbd;
        $this->cbd          = $cbd;
        $this->guachi       = guachi::iniciar();
        $this->controlador  = controlador::iniciar();
        $this->limpiador    = limpiador::iniciar();
        $this->request      = $this->limpiador->recolectar($this->controlador->get_modulo(),tipo_db,true);
        $this->limit        = '';
        $this->metatags     = metadata::iniciar();
    }

    /**
     * __destruct
     *
     * Destructor, destruye automaticamente la clase.
     *
     * @access public
     */
    public function __destruct() {

    }

    /**
     * Inicia la instancia de la clase
     * @return object
     */
    public static function iniciar() {
        if (!self::$instancia instanceof self) {
            self::$instancia = new self;
        }
        return self::$instancia;
    }

    /**
     * Método magico __clone
     */
    public function __clone() {
        trigger_error('Operación Invalida:' .
                ' clonación no permitida', E_USER_ERROR);
    }

    /**
     * Método magico __wakeup
     */
    public function __wakeup() {
        trigger_error('Operación Invalida:' .
                ' deserializar no esta permitido ' .
                get_class($this) . " Class. ", E_USER_ERROR);
    }

    /**
     * Método Warnings
     *
     * Establece mensajes de error a mostrar luego de una peticion POST
     */
    public function Warnings($errs) {
        $this->Warnings = $errs;
    }

    /**
     * Método setNameOfComment
     *
     * Establece el value del campo name en el formulario de comentarios
     */
    public function setNameOfComment($name) {
        $this->name = $name;
    }

    /**
     * Método setComment
     *
     * Establece el comentario del formulario de comentarios
     */
    public function setComment($comment) {
        $this->commentPost = $comment;
    }

    /**
     * Método resultPerPage
     *
     * Setea los resultados por pagina 
     */  
    public function resultPerPage($num) {
        $this->perpage = $num;
    }

    /**
     * Método isPage
     *
     * Retorna el numero de pagina solicitada
     */ 
    public function isPage() {
        $page = isset($this->request->arg) ? $this->request->arg : false;
        if( isset($page) and $page === 'page' ){
            $numberPage = isset($this->request->arg1) ? $this->request->arg1 : 1;
            if ( is_numeric($numberPage) ) {
                return $numberPage;
            }
            return false;
        }
        return false;
    }

    /**
     * Método getUrl
     *
     * Retorna la url solicitada o false 
     */ 
    private function getUrl() {
        if( isset($this->request->arg) ){
            return $this->request->arg;
        }
        return false;
    }

    /**
     * Método isPost
     *
     * Retorna false si un posts no existe
     */  
    public function isPost() {
        $url = $this->getUrl();
        if( $url ){
            $sql    = "select id_post from posts where url='$url' and status='t' ";
            $result = $this->cbd->get_var($sql);
            if($result) {
                return $result;
            }
            return false;
        }
    }

    /**
     * Método getTotalPages
     *
     * Retorna el total de paginas
     */  
    private function getTotalPages() {
        $sql_total = "select count(id_post) from posts where status='t' ";
        $recordsTotal = $this->cbd->get_var($sql_total);
        if( $recordsTotal ){
            return  ($recordsTotal > $this->perpage ) ? ceil($recordsTotal/$this->perpage) : 1;
        }
        return false;
    }

    /**
     * Método getDescription
     *
     * Retorna la descripcion del post, recortando el texto total
     */ 
    private function getDescription($text, $length = 590){
        $text = strip_tags($text);
        if(strlen($text) > $length) {
            $text = substr($text, 0, strpos($text, ' ', $length)) .' ...' ;
        }
        return $text;
    }

    /**
     * Método getPages
     *
     * Retorna el contanido para paginar
     */
    public function getPages() {
        $currentPage   = ($this->isPage()) ? $this->isPage() : 1 ;
        $offset = ( ( $currentPage - 1 ) * $this->perpage );
        $sql = "select posts.*,users.fullname from posts "
              ."inner join users on (users.id_user=posts.id_user) "
              ."where posts.status='t' "
              ."order by posts.id_post desc "
              ."limit $this->perpage offset $offset";
        $results = $this->cbd->get_results($sql);
        if ($results) {
            foreach ($results as $campo => $valor) {
                $datePublished = date("c",strtotime($valor->datePublished));
                $dateModified  = date("c",strtotime($valor->dateModified));
                $data['pages'][] = [
                        'site_url'      => site_url,
                        'url'           => $valor->url,
                        'title'         => $valor->title,
                        'description'   => $this->getDescription($valor->articleBody),
                        'datePublished' => $datePublished,
                        'dateModified'  => $dateModified,
                        'humanDate'     => $this->humanDate($valor->datePublished),
                        'wordCount'     => $valor->wordCount,
                        'author'        => $valor->fullname,
                        'readingTime'   => $this->readingTime($valor->articleBody)
                ];
            }
            $this->metatags->setPublishedTime(date("c",strtotime($results[0]->datePublished)));
            $this->metatags->setModifiedTime(date("c",strtotime($results[0]->dateModified)));
            $totalPages    = $this->getTotalPages();
            $prevPage      = ($currentPage > 1) ? ($currentPage - 1) : '';
            $nextPage      = ($currentPage < $totalPages) ? ($currentPage + 1) : '';          
            $data = $data + [
                        'totalPages'    => $totalPages,
                        'currentPage'   => $currentPage,
                        'prevPage'      => $prevPage,
                        'nextPage'      => $nextPage
            ];
            return $data;
        }
        return false;
    }

    /**
     * Método getComments
     *
     * Retorna los comentarios de un posts
     */
    private function getComments($url) {
        /*@TODO despues corrijo esta redundancia*/
        function humanDate($datetime) {
             $date = spanishdate($datetime);
             return sprintf('%d de %s, %d a las %s', $date->fecha, $date->mes,$date->anio, $date->hora);
        }
        $sql = "select name,comment,comments.datePublished "
              ."from comments "
              ."inner join posts on (posts.id_post=comments.id_post) "
              ."where posts.url='$url' "
              ."order by id_comment desc ";
        $results = $this->cbd->get_results($sql);
        if ($results) {
            foreach ($results as $campo => $valor) {
                $data[]=[
                    'initial'       => ucfirst(substr($valor->name,0, 1)),
                    'name'          => $valor->name,
                    'comment'       => $valor->comment,
                    'datePublished' => humanDate($valor->datePublished)
                ];
            }
            return $data;
        }
        return false;        
    }

    /**
     * Método getTags
     *
     * Retorna las etiquetas de el posts
     */
    private function getTags($url) {
        $sql = "select tag from tags "
                ."join posts_tagged on (posts_tagged.id_tag=tags.id_tag) "
                ."join posts        on (posts.id_post=posts_tagged.id_post) "
                ."where posts.url='$url' and status='t' ";
        $results = $this->cbd->get_results($sql);
        if ($results) {
            foreach ($results as $campo => $valor) {
                $data[]=[
                     'tag'=>$valor->tag
                ];
            }
            return $data;
        }
        return false;        
    }

    /**
     * Método getCategories
     *
     * Retorna las categorias de el posts
     */
    public function getCategories($url) {
        $sql = "select category from categories "
                ."join posts_categories on (posts_categories.id_category=categories.id_category) "
                ."join posts            on (posts.id_post=posts_categories.id_post) "
                ."where posts.url='$url' and status='t' ";
        $results = $this->cbd->get_results($sql);
        if ($results) {
            foreach ($results as $campo => $valor) {
                $data[]=[
                     'category'=>$valor->category
                ];
            }
            return $data;
        }
        return false;
    }

    /**
     * Método getFirstImage
     *
     * Retorna la primera imagen de un string HTML
     */ 
    private function getFirstImage($str){
        preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $str, $matches);
        return isset($matches[1][0])?$matches[1][0]:'/img/default.png';
    }

    /**
     * Método getContent
     *
     *
     * Retorna el contenido para renderizar la bitacora
     */
    public function getContentOfPost() {
        $url = $this->getUrl();
        $sql = "select posts.*,users.fullname from posts "
              ."inner join users on (users.id_user=posts.id_user) "
              ."where posts.url='$url'";
        $result = $this->cbd->get_row($sql);
        if ($result) {
            $datePublished = date("c",strtotime($result->datePublished));
            $dateModified  = date("c",strtotime($result->dateModified));
            $description   = $this->getDescription($result->articleBody, 200);
            /*seteo la metadata*/
            $this->metatags->setCanonicalUrl(site_url .'/posts/'.$result->url);
            $this->metatags->setImage($this->getFirstImage($result->articleBody));
            $this->metatags->setTitle($result->title);
            $this->metatags->setDescription($description);
            $this->metatags->setPageType('article');
            $this->metatags->setPublishedTime($datePublished);
            $this->metatags->setModifiedTime($dateModified);
            /**/
            $category = $this->getCategories($result->url);
            $tag      = $this->getTags($result->url);
            $comment  = $this->getComments($result->url);
            $warnings = $this->Warnings;
            $data = [
                'id_post'       => $result->id_post,
                'site_url'      => site_url,
                'url'           => $result->url,
                'title'         => $result->title,
                'description'   => $description,
                'articleBody'   => $result->articleBody,
                'datePublished' => $datePublished,
                'dateModified'  => $dateModified,
                'humanDate'     => $this->humanDate($result->datePublished),
                'wordCount'     => $result->wordCount,
                'author'        => $result->fullname,
                'readingTime'   => $this->readingTime($result->articleBody),
                'in_category'   => ($category) ? 't' : '',
                'category'      =>  $category,
                'in_tag'        => ($tag)      ? 't' : '',
                'tag'           =>  $tag,
                'in_comment'    => ($comment)  ? 't' : '',
                'comment'       =>  $comment,
                'warningExists' => ($warnings) ? 't' : '',
                'warnings'      => $this->Warnings,
                'name'          => $this->name,
                'commentPost'   => $this->commentPost
            ];
            return $data;
        }
        return false;
    }

    /**
     * Método humanDate
     *
     *
     * Retorna la fecha en formato humano
     */    
    public function humanDate($datetime) {
         $date = spanishdate($datetime);
         return sprintf('%d de %s, %d', $date->fecha, $date->mes,$date->anio);
    }

    /**
     * Método notFount
     *
     *
     * Retorna 404 y el template correspondiente
     */    
    public function notFount() {
        /*el header 404*/
        header("HTTP/1.0 404 Not Found");
        return 'posts/404';       
    }

    /**
     * Método get_request
     *
     *
     * Retorna el template a usar
     */    
    public function getTemplate() {
        if ( $this->request->modulo === 'posts' ) {
            if (isset($this->request->arg)) {
                if (is_numeric($this->isPage())) {
                    if ($this->isPage() > $this->getTotalPages() ) {
                       return $this->notFount();
                    }
                    return 'posts/page';
                }elseif ($this->isPost()) {
                    return 'posts/post';
                }else{
                    return $this->notFount();
                }
            }
            return 'posts/page';
        }
    }

    /**
     * Método getContent
     *
     *
     * Retorna el contenido para renderizar la bitacora
     */
    public function getContent() {
        if(is_numeric($this->isPage())){
            return $this->getPages();
        }elseif($this->isPost()){
            return $this->getContentOfPost();
        }
        return $this->getPages();
    }

    /**
     * Método wordCount
     *
     *
     * Cuenta el numero de palabras en u post
     */
    public function wordCount( $str ) {
        return str_word_count(strip_tags($str));
    }

    /**
     * Método readingTime
     *
     *
     * Calcula el tiempo de lectura de un post
     * @TODO , buena tarea para los niños
     */
    public function readingTime($str) {
        $words      = $this->wordCount($str);
        $mimute     = floor($words / 200);
        return $mimute . ' minuto' . ($mimute == 1 ? '' : 's');
    }   
}





















