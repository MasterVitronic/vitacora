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
            $sql    = "select id_post from posts where url='$url'";
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
    public function getTotalPages() {
        $sql_total = "select count(id_post) from posts where status='t' ";
        $recordsTotal = $this->cbd->get_var($sql_total);
        if( $recordsTotal ){
            return  ($recordsTotal > $this->perpage ) ? ($recordsTotal/$this->perpage) : 1;
        }
        return false;
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
                $data['pages'][] = [
                        'url'           => $valor->url,
                        'title'         => $valor->title,
                        'description'   => $valor->description,
                        'datePublished' => date("c",strtotime($valor->datePublished)),
                        'humanDate'     => date("d M, Y",strtotime($valor->datePublished)),
                        'dateModified'  => date("c",strtotime($valor->dateModified)),
                        'wordCount'     => $this->wordCount($valor->articleBody),
                        'author'        => $valor->fullname,
                        'readingTime'   => $this->readingTime($valor->articleBody)
                ];
            }
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
            $data = [
                'title'         => $result->title,
                'description'   => $result->description,
                'articleBody'   => $result->articleBody,
                'datePublished' => date("c",strtotime($result->datePublished)),
                'humanDate'     => date("d M, Y",strtotime($result->datePublished)),
                'dateModified'  => date("c",strtotime($result->dateModified)),
                'wordCount'     => $this->wordCount($result->articleBody),
                'author'        => $result->fullname,
                'readingTime'   => $this->readingTime($result->articleBody)
            ];
            return $data;
        }
        return false;
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
        return 'paginas/posts/404';       
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
                    return 'paginas/posts/page';
                }elseif ($this->isPost()) {
                    return 'paginas/posts/post';
                }else{
                    return $this->notFount();
                }
            }
            return 'paginas/posts/page';
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





















