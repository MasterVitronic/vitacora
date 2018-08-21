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

class guachi {

    /**
     * Instancia para el patrón de diseño singleton (instancia única)
     * @var object instancia
     * @access private
     */
    private static $instancia = null;

    private function __construct() {

    }

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
        trigger_error("Operación Invalida:" .
                " clonación no permitida", E_USER_ERROR);
    }

    /**
     * Método magico __wakeup
     */
    public function __wakeup() {
        trigger_error("Operación Invalida:" .
                " deserializar no esta permitido " .
                get_class($this) . " Class. ", E_USER_ERROR);
    }

    /**
     * ip
     *
     * Si es posible retorna la ip del visitante
     *
     * @access public
     * @return string
     */
    public function g_ip() {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $_SERVER["REMOTE_ADDR"] = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        return $ip;
    }

    /**
     * g_crc
     *
     * retorna un CRC (Cyclic redundancy check) aleatorio para usar
     *
     * @access public
     * @return string
     */
    public function g_crc() {
        return hash('crc32b', md5(uniqid(rand(), 1)));
    }
    
    /**
     * ajax_request
     *
     * Chequea si el httprequest es ajax
     *
     * @access public
     * @return boolean
     */
    public function g_ajax_request() {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' and !empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            return true;
        }
        return false;
    }
    
    /**
     * g_mensajes
     *
     * metodo para mostrar mensajes
     *
     * @access public
     * @return string
     */
    public function g_mensajes($caso = null, $input = false) {
        $mensajes = new stdClass();
        switch ($caso) {
            case 'nulo':
                $mensajes->id_input = $input;
                $mensajes->mensaje = gettext('Faltan datos , imposible proceder.');
                break;
            case 'existe':
                $mensajes->id_input = $input->campo;
                $mensajes->mensaje = gettext("El valor $input->valor ya esta" .
                        ' registrado, imposible proceder.');
                break;
            case 'ok':
                $mensajes->mensaje = 'ok';
                break;
            default:
                $mensajes->id_input = 'id';
                $mensajes->mensaje = gettext('ERROR desconocido (posiblemente humano), no' .
                        ' pude hacer nada, sorry :-(');
                break;
        }
        echo json_encode($mensajes);
        unset($mensajes);
    }
    
    /**
     * g_mensaje
     *
     * metodo para mostrar mensajes arbitrarios
     *
     * @access public
     * @return string
     */
    public function g_mensaje($mensaje,$input = false) {
        $mensajes = new stdClass();
        $mensajes->mensaje = $mensaje;
        $mensajes->id_input = $input;
        echo json_encode($mensajes);
        unset($mensajes);
    }
    
    /**
     * g_msg_mod_error
     *
     * metodo para mostrar mensajes en el modulo error
     *
     * @access public
     * @return string
     */
    public function g_msg_mod_error($mensaje = false, $accion = false) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $msg_default = "<p>La pagina que usted esta tratando de alcanzar, no existe.</p>\n";
        switch ($accion) {
            case 'mostrar':
                $_SESSION['msg_mod_error'] = $mensaje."\n";
                require_once(ROOT . DS . 'modulos' . DS . 'error.php');
                break;
            case 'no_autorizado':
                $mensaje = "<p>La pagina que usted esta tratando de alcanzar, no esta disponible para usted.</p>\n";
                $_SESSION['msg_mod_error'] = $mensaje;
                require_once(ROOT . DS . 'modulos' . DS . 'error.php');
                break;
            default:
                echo isset($_SESSION['msg_mod_error']) ? $_SESSION['msg_mod_error'] : $msg_default;
                unset($_SESSION['msg_mod_error']);
                break;
        }
    }

    /**
     * set_cache_header
     *
     * establece un header cache con un tiempo determinado
     *
     * @access public
     * @return string
     */
    public function set_cache_header($seconds = false) {
        $seconds_to_cache = ($seconds) ? $seconds : $seconds_to_cache = 3600;
        $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$seconds_to_cache");
    } 

}

