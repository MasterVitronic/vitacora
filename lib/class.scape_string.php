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

class limpiador {

    /**
     * Instancia para el patrón de diseño singleton (instancia única)
     * @var object instancia
     * @access private
     */
    private static $instancia = null;

    /* Contructor, inicializa la clase
     *
     * ENTRADA: -
     * SALIDA:  -
     * ERROR:   -
     */

    private function __construct() {

    }

    public function __destruct() {
        unset($this->campo);
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
     *
     * recolectar Un recolector y escapador de datos.
     *
     * ENTRADA : array() en bruto,tipo de db, modo
     *      tipos:
     *          limpio = limpia de manera generica el array
     *          pgsql = escapa el array para PostgreSQL
     *          en_bruto = solo convierte el array en objetos
     *          sqlite = escapa el array para SQlite ||
     *      modos:
     *          1 retorna la salida convertida en objetos
     *          false solo retorna, la salida escapada ||
     *
     * @access public
     * @return array/object
     *      la entrada es escapada y retornada en objetos o array segun el modo
     * @TODO probablemente tenga que reajustar el sqlite cuendo funcione con sqlcliper
     */
     
    static function recolectar($_datos, $tipo, $modo = false) {
        switch ($tipo) {
            case 'limpio':
                foreach ($_datos as $campo => $valor) {
                    $limpiado = trim(htmlentities(addslashes($valor)));
                    $data[$campo] = $limpiado;
                }
                break;
            case 'pgsql':
                foreach ($_datos as $campo => $valor) {
                    if (is_string($valor)) {
                        $limpiado = trim(pg_escape_string($valor));
                        $data[$campo] = $limpiado;
                    }
                }
                break;
            case 'en_bruto':
                foreach ($_datos as $campo => $valor) {
                    $data[$campo] = $valor;
                }
                break;
            case 'sqlite':
                foreach ($_datos as $campo => $valor) {
                    if(is_string($valor)){
                        $limpiado = trim(SQLite3::escapeString($valor));
                        $data[$campo] = $limpiado;
                    }
                }
                break;
        }
        if (isset($data)) {
            if ($modo === true) {
                //return (object) ['campo' => (object) $data];
                return (object) $data;
            } else {
                return $data;
            }
        }
    }

    static function clear_data($valor) {
        if (tipo_db === 'pgsql') {
            return trim(pg_escape_string($valor));
        } elseif (tipo_db === 'sqlite') {
            return trim(SQLite3::escapeString($valor));
        }
        return false;
    }

}
