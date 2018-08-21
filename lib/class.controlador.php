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

class controlador {

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
     * Verifica si un modulo existe
     * 
     * @access public
     * @return boolean
     */
    public function check_modulo($modulo) {
        return is_file(ROOT . 'modulos' . DS . $modulo . '.php');
    }
    
    /**
     * Inicializa/Setea los modulos de Guachi
     * 
     * @access public
     * @return array
     */
    public function get_modulo() {
        $modulo = strtolower(filter_input(INPUT_GET, 'modulo', FILTER_SANITIZE_URL));
        $path = parse_url($modulo)['path'];
        $modulo2 = explode('/', $path);
        $modulo3 = array_filter($modulo2);
        if (empty($modulo3)) {
            return ['modulo' => modulo_inicio];
        }
        return array_filter([
            'modulo'    => array_shift($modulo3),
            'arg'       => array_shift($modulo3),
            'arg1'      => array_shift($modulo3),
            'arg2'      => array_shift($modulo3),
            'arg3'      => array_shift($modulo3)
        ]);
    }

}
