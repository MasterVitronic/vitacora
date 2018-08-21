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

class posts {
    
    private $cbd;
    private $guachi;
    private $controlador;
    private $request;
    private $limpiador;

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
    }

    /**
     * __destruct
     *
     * Destructor, destruye automaticamente la clase.
     *
     * @access public
     */
    public function __destruct() {
        unset($this->cbd);
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
     * Método isPage
     *
     * Retorna el numero de pagina solicitada
     */ 
    public function isPage() {
        $page = isset($this->request->arg)?$this->request->arg:false;
        if(isset($page) and $page === 'page' ){
            $numberPage = isset($this->request->arg1)?$this->request->arg1:false;
            if (ctype_digit($numberPage) ) {
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
        if(isset($this->request->arg)){
            return $this->request->arg;
        }
        return false;
    }

    /**
     * Método postExists
     *
     * Retorna false si un posts no existe
     */  
    public function postExists() {
        $url = $this->getUrl();
        if( $url ){
            $sql    = "select id_post from posts where url='$url'";
            if($this->cbd->get_var($sql)) {
                return true;
            }
            return false;
        }
    }
    
    /**
     * Método getBody
     *
     * Retorna el Body del Post
     */    
    public function getBody() {
        $url = $this->getUrl();
        if( $url ){
            $sql = "select articleBody from posts where url='$url'";
            return $this->cbd->get_var($sql);
        }
        return false;
    }

    /**
     * Método get_request
     *
     *
     * Retorna el template a usar
     */    
    public function getTemplate() {
        if ($this->request->modulo === 'posts') {
            if (isset($this->request->arg)) {
                if ($this->isPage() or $this->postExists()) {
                    return 'paginas/posts/post';
                }else{
                    /*el header 404*/
                    header("HTTP/1.0 404 Not Found");                    
                    return 'paginas/posts/not-found';
                }
            }
            return 'paginas/posts/index';
        }
    }

    /**
     * Método wordCount
     *
     *
     * Cuenta el numero de palabras en u post
     */
    public function wordCount($str) {
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
        return $mimute . ' minuto' . ($m == 1 ? '' : 's');
    }
    
}





















