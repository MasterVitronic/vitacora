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

class auth {

    private $cbd;
    private $username;
    private $id_user;

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
        if (!isset($_SESSION)) {
            session_start();
        }
        global $cbd;
        $this->cbd = $cbd;
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
     * metodo key
     *
     * @access private
     */
    private function key($mode) {
        switch ($mode) {
            case 'exit':
                return (session_destroy()) ? true : false;
            case 'login':
                $ip_address = get_ip();
                if($ip_address){
                    $_SESSION = [
                        'username'          => $this->username,
                        'id_user'           => $this->id_user,
                        'session_timeout'   => (time() + session_timeout),
                        'ip_address'        => $ip_address
                    ];
                }
                return true;
        }
        return false;
    }

    /**
     * metodo logIn
     *
     * @access public
     */
    public function logIn($username, $password) {
        $sql  =  'select id_user,status,password '
                 ."from users where username='$username' and status = 't'";
        $consulta = $this->cbd->get_row($sql);
        if(isset($consulta->id_user)){
            if(password_verify($password , $consulta->password)){
                $this->username  = $username;
                $this->id_user   = $consulta->id_user;
                $this->key('login');
            }
        }
        return false;
    }

    /**
     * metodo hashPass
     *
     * @access public
     */
    public function hashPass($password) {
        return  password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * metodo logOut
     *
     * @access public
     */
    public function logOut() {
        return $this->key('exit') == true ? true : false;
    }

    /**
     * metodo idUser
     *
     * @access public
     */
    public function idUser() {
        return isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : false;
    }

    /**
     * metodo sessionIsValid
     *
     * @access public
     */
    public function sessionIsValid() {
        if( isset($_SESSION['id_user']) and isset($_SESSION['session_timeout']) ){
            if( $_SESSION['session_timeout'] > time() ){
                $_SESSION['session_timeout'] = (time() + session_timeout);
                return true;
            }
            $this->logOut();
        }
        return false;
    }

    /**
     * metodo isLogged
     *
     * @access public
     */
    public function isLogged() {
       return ($this->sessionIsValid()) ? true : false ;
    }

    /**
     * metodo userInfo
     *
     * @access public
     */
    public function userInfo() {
        if ( $this->isLogged() ) {
            return (object) [
                'username'  => $_SESSION['username'],
                'id_user'   => intval($_SESSION['id_user'])
            ];
        }
        return false;
    }

    /**
     * metodo setCsrf
     *
     * @access public
     */
    public function setCsrf() {
        $_SESSION['csrf'] = bin2hex(openssl_random_pseudo_bytes(32));
    }

    /**
     * metodo getCsrf
     *
     * @access public
     */
    public function getCsrf() {
        return $_SESSION['csrf'];
    }
    
    /**
     * metodo csrfIsValid
     *
     * @access public
     */
    public function csrfIsValid($csrf) {
        return hash_equals($this->getCsrf(), $csrf);
    }

}
